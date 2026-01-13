<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use App\Traits\OptimizedDatabaseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    use OptimizedDatabaseTrait;
    public function index()
    {
        return $this->executeQuery(function () {
            $customers = User::select(['id', 'name', 'email', 'phone', 'is_active', 'created_at'])
                ->withoutUser()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('admin.users.customers.index', compact('customers'));
        });
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'phone' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!phoneNumberValidation($value)) {
                        $fail('Please enter a valid Sri Lankan phone number.');
                    }
                },
                Rule::unique('users', 'phone')->where(function ($query) {
                    return $query->where('created_by', 'admin');
                }),
            ],
            'email' => [
                'nullable',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('created_by', 'admin');
                }),
            ],
        ]);

        $request->merge(['phone' => phoneNumberValidation($request->phone)]);

        if (empty($request->email)) {
            $request->merge(['email' => $request->phone . '@phone.user']);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'created_by' => 'admin',
                'password' => Hash::make('12345678'),
            ]);

            return response()->json(['success' => true, 'message' => 'Customer created successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            Log::error('Customer creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the customer'
                // 'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAddress(Request $request)
    {
        $addresses = Address::where('user_id', $request->user_id)->get();


        return response()->json(['success' => true, 'addresses' => $addresses], 200);
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'address' => 'required',
            // 'state' => 'required',
            // 'zip_code' => 'required',
            'city' => 'required',
            'country' => 'required',
            'phone' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!phoneNumberValidation($value)) {
                        $fail('Please enter a valid Sri Lankan phone number.');
                    }
                },
            ],
        ]);

        $request->merge(['phone' => phoneNumberValidation($request->phone)]);

        try {
            $address = Address::create($request->all());
            return response()->json(['success' => true, 'message' => 'Address created successfully', 'address' => $address], 200);
        } catch (\Exception $e) {
            Log::error('Address creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the address'
            ], 500);
        }
    }

    public function deleteAddress(Request $request)
    {
        $request->validate([
            'address_id' => 'required',
        ]);

        try {
            $address = Address::find($request->address_id);
            $address->delete();
            return response()->json(['success' => true, 'message' => 'Address deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Address deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the address'
            ], 500);
        }
    }

    public function activateCustomer(User $customer)
    {
        try {
            if ($customer->is_active) {
                $customer->is_active = false;
                $message = 'Customer deactivated successfully';
            } else {
                $customer->is_active = true;
                $message = 'Customer activated successfully';
            }
            $customer->save();

            return response()->json([
                'status' => true,
                'message' => $message
            ], 201);
        } catch (\Exception $e) {
            Log::error('Customer activation failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }


    public function destroy(User $customer)
    {
        try {
            if ($customer->orders()->exists()) {
                // Log deletion attempt with orders
                activity('customer_management')
                    ->causedBy(auth()->user())
                    ->performedOn($customer)
                    ->withProperties([
                        'operation_type' => 'delete_attempt_failed',
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'customer_email' => $customer->email,
                        'reason' => 'customer_has_orders',
                        'orders_count' => $customer->orders()->count()
                    ])
                    ->log("Failed to delete customer {$customer->name} ({$customer->email}) - customer has orders");

                return response()->json([
                    'status' => false,
                    'message' => 'Customer has orders.',
                ], 400);
            }

            // Log customer deletion before deleting
            activity('customer_management')
                ->causedBy(auth()->user())
                ->performedOn($customer)
                ->withProperties([
                    'operation_type' => 'delete',
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_phone' => $customer->phone,
                    'registration_date' => $customer->created_at,
                    'last_login' => $customer->last_login_at ?? 'Never',
                    'is_active' => $customer->is_active
                ])
                ->log("Deleted customer {$customer->name} ({$customer->email})");

            $customer->delete();
            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Customer delete failed: ' . $e->getMessage());

            // Log deletion error
            activity('customer_management')
                ->causedBy(auth()->user())
                ->performedOn($customer)
                ->withProperties([
                    'operation_type' => 'delete_error',
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'error' => $e->getMessage()
                ])
                ->log("Error deleting customer {$customer->name} ({$customer->email}): {$e->getMessage()}");

            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
