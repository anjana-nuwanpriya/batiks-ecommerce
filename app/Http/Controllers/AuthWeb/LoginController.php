<?php

namespace App\Http\Controllers\AuthWeb;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Artesaos\SEOTools\Facades\SEOTools;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        SEOTools::setTitle('Login');
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());

        return view('frontend.auth.login');
    }

    public function showVerifyForm(){
        if(session('email')){
            return view('frontend.auth.verify');
        }else{
            return redirect()->route('user.login');
        }
    }


    public function login(Request $request)
    {
        $type = $request->type;

        if (!in_array($type, ['email', 'phone'])) {
            toast()->error('Invalid login type.');
            return redirect()->back();
        }

        $this->validateLoginRequest($request, $type);

        if ($type == 'phone') {
            $request->merge(['phone' => phoneNumberValidation($request->phone)]);
        }

        $field = $type;
        $value = $request->$field;

        $user = User::where($field, $value)->first();

        if (!$user) {
            toast()->error("Invalid $type or password.");
            return redirect()->back()->with('type', $type);
        }

        if (!$user->is_active) {
            toast()->error('Your account is blocked.');
            return redirect()->back()->with('type', $type);
        }

        // Attempt authentication
        $credentials = [
            $field => $value,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if user is staff and redirect to admin dashboard
            if ($user->staff) {
                return redirect()->route('admin.dashboard');
            }

            toast()->success('You have been logged in successfully.');
            return redirect()->intended('user/dashboard');
        }

        toast()->error("Invalid $type or password.");
        return redirect()->back()->with('type', $type);
    }

    /**
     * Validate the login request based on the login type.
     *
     * @param Request $request
     * @param string $type
     * @return void
     */
    private function validateLoginRequest(Request $request, string $type)
    {
        $rules = [
            'password' => 'required|string|min:6',
        ];

        if ($type == 'email') {
            $rules['email'] = 'required|string|email|max:255';
        } else { // phone
            $rules['phone'] = [
                'required',
                'string',
                function($attribute, $value, $fail) {
                    if (!phoneNumberValidation($value)) {
                        $fail('Please enter a valid phone number.');
                    }
                }
            ];
        }

        $request->validate($rules);
    }


    public function logout()
    {
        // Log out the user
        Auth::logout();

        session()->invalidate();

        session()->regenerateToken();

        toast()->success('You have been logged out successfully.');
        // Redirect to the home page with a success message
        return redirect()->route('home');
    }


}
