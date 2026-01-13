<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        $staffs = Staff::where('user_id', '!=', 1)->get();
        return view('admin.users.users.index', compact('staffs', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'roles' => 'array|required', // array
            'name' => 'required',
            'gender' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        try{

            DB::transaction(function () use ($request) {
                // Create a new user
                $user = new User();
                $user->name = $request->name;
                $user->phone = $request->phone;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->is_active = $request->status;
                $user->save();

                // Create a new staff associated with the user
                $staff = new Staff();
                $staff->gender = $request->gender;
                $user->staff()->save($staff);

                // Assign the role
                $user->assignRole(Role::whereIn('id', $request->roles)->pluck('name')->toArray());
            });

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
            ]);

        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the user'
            ], 500);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();

        $viewpage = view('admin.users.users.edit', compact('staff', 'roles'))->render();

        return response()->json([
            'status' => true,
            'data' => ['data' => $viewpage]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'roles' => 'array|required', // array
            'name' => 'required',
            'gender' => 'required',
            'email' => 'required|email|unique:users,email,'.$staff->user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        try{

            DB::transaction(function () use ($request, $staff) {
                // Update the user
                $staff->user->name = $request->name;
                $staff->user->phone = $request->phone;
                $staff->user->email = $request->email;
                $staff->user->is_active = $request->status;
                if ($request->filled('password')) {
                    $staff->user->password = Hash::make($request->password);
                }
                $staff->user->save();

                // Update the staff
                $staff->gender = $request->gender;
                $staff->save();

                // Update the role
                $staff->user->roles()->detach();
                $staff->user->assignRole(Role::whereIn('id', $request->roles)->pluck('name')->toArray());
            });

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the user'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        try{
            DB::transaction(function () use ($staff) {
                $staff->user->delete();
                $staff->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);

        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the user'
            ], 500);
        }
    }


    public function changeAccountInfo()
    {
        return view('admin.account-setting');
    }

    public function updateAccountInfo(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.auth()->user()->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $request->validate([
            'phone' => ['nullable', function ($attribute, $value, $fail) {
                if (!empty($value) && !phoneNumberValidation($value)) {
                    $fail('The phone number format is invalid.');
                }
            }]
        ]);

        if($request->has('phone') && !empty($request->phone)){
            $request->merge(['phone' => phoneNumberValidation($request->phone)]);
        }

        try{
            DB::transaction(function () use ($request) {
                $user = User::find(auth()->user()->id);
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                if($request->has('password')){
                    $user->password = Hash::make($request->password);
                }
                $user->save();
            });

            return response()->json([
                'success' => true,
                'message' => 'Account info updated successfully',
            ]);

        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error updating account info: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the account info'
            ], 500);
        }
    }
}