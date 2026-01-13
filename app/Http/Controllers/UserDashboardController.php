<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Address;
use App\Models\Order;
use App\Models\Wishlist;
use App\Traits\OptimizedDatabaseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Artesaos\SEOTools\Facades\SEOTools;

class UserDashboardController extends Controller
{
    use OptimizedDatabaseTrait;

    public function index(){
        $title = config('app.name') . ' | Dashboard';
        $description = 'Dashboard';
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());

        return $this->executeQuery(function () {
            $orders = Order::select(['id', 'code', 'payment_status', 'delivery_status', 'grand_total', 'created_at'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $address = Address::select(['id', 'address', 'city', 'state', 'country', 'zip_code', 'phone'])
                ->where('user_id', Auth::id())
                ->where('is_default', true)
                ->first();

            return view('frontend.customer.dashboard', compact('orders', 'address'));
        });
    }

    public function viewOrders(){
        $title = config('app.name') . ' | Order History';
        $description = 'Order History';
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());

        return $this->executeQuery(function () {
            $orders = Order::select(['id', 'code', 'payment_status', 'delivery_status', 'grand_total', 'created_at'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('frontend.customer.order-history', compact('orders'));
        });
    }

    public function viewOrder($id)
    {
        $title = config('app.name') . ' | Order Details';
        $description = 'Order Details';
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());

        return $this->executeQuery(function () use ($id) {
            $order = Order::with(['items.product:id,name', 'user:id,name,email'])
                ->where('user_id', Auth::id())
                ->find($id);

            if(!$order){
                return redirect()->route('user.order-list')->with('error', 'Order not found');
            }

            return view('frontend.customer.order', compact('order'));
        });
    }

    public function manageAccount(){
        $title = config('app.name') . ' | Manage Account';
        $description = 'Manage Account';
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());

        return $this->executeQuery(function () {
            $address = Address::select(['id', 'address', 'city', 'state', 'country', 'postal_code', 'phone', 'is_default'])
                ->where('user_id', Auth::id())
                ->orderBy('is_default', 'desc')
                ->get();

            return view('frontend.customer.manage_profile', compact('address'));
        });
    }

    public function wishlist(){

        $title = config('app.name') . ' | Wishlist';
        $description = 'Wishlist';
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());

        $wishlist = Wishlist::where('user_id', Auth::id())->get();
        return view('frontend.customer.wishlist', compact('wishlist'));
    }

    /**
     * Store Address
     */
    public function storeAddress(Request $request){

        $request->validate([
            'address' => 'required',
            'zip_code' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'phone' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!phoneNumberValidation($value)) {
                    $fail('Please enter a valid Sri Lankan phone number.');
                }
            }],
        ]);

        try {
            $address = new Address();
            $address->user_id = auth()->user()->id;
            $address->address = $request->address;
            $address->zip_code = $request->zip_code;
            $address->city = $request->city;
            $address->state = $request->state;
            $address->country = $request->country;
            $address->phone = $request->phone;
            $address->save();

            return response()->json(['success' => 'Address added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Set Default Address
     */
    public function setDefaultAddress(Request $request){
        // Set all addresses to not default
        Address::where('user_id', auth()->id())->update(['is_default' => false]);

        // Set selected address as default
        $address = Address::find($request->id);
        $address->is_default = true;
        $address->save();

        return response()->json(['success' => 'Address set as default successfully'], 200);
    }

    /**
     * Delete Address
     */
    public function deleteAddress(Request $request){
        $address = Address::find($request->id);
        $address->delete();

        return response()->json(['success' => 'Address deleted successfully'], 200);
    }

    /**
     * Upload Slip
     */
    public function uploadSlip(Request $request, $id){

        $request->validate([
            'payment_proof' => 'required',
        ]);

        try {
            $order = Order::find($id);
            if($order->payment_status == 'pending'){
                Helper::updateImage($order, 'payment_proof');
            }

            return response()->json(['success' => 'Payment proof uploaded successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error uploading payment proof: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update Profile
     */
    public function updateProfile(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
            'phone' => 'required',
            'password' => 'nullable|min:8,confirmed',
        ]);


    }

    /**
     * Edit Address View
     */
    public function editAddressView(Request $request){
        try {
            $address = Address::find($request->id);
            if (!$address) {
                throw new \Exception('Address not found');
            }

            $view = view('components.edit-address', compact('address'))->render();
            return response()->json(['view' => $view], 200);

        } catch (\Exception $e) {
            Log::error('Error getting address edit view: ' . $e->getMessage());
            return response()->json(['message' => 'Error loading address details'], 500);
        }
    }

    /**
     * Update Address
     */
    public function updateAddress(Request $request, $id){
        $request->validate([
            'address' => 'required',
            'zip_code' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'phone' => 'required',
        ]);

        $address = Address::find($id);
        $address->address = $request->address;
        $address->zip_code = $request->zip_code;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->country = $request->country;
        $address->phone = $request->phone;
        $address->save();

        return response()->json(['success' => 'Address updated successfully'], 200);
    }
}
