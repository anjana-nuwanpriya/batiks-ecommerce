<?php

namespace App\Http\Controllers\AuthWeb;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Utilities\HutchSmsUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Artesaos\SEOTools\Facades\SEOTools;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        SEOTools::setTitle('Register');
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());
        return view('frontend.auth.register');
    }

    public function showForgotPasswordForm()
    {
        SEOTools::setTitle('Forgot Password');
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());
        return view('frontend.auth.forgot-password');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!phoneNumberValidation($value)) {
                    $fail('Please enter a valid Sri Lankan phone number.');
                }
            }],
            'email' => 'nullable|email',
            'password' => 'required|string|min:7|confirmed',
            'terms' => 'required|accepted',
        ]);

        $phone = phoneNumberValidation($request->phone);
        $email = $request->email;
        $isEmptyEmail = empty($email);

        // If email is empty, create a placeholder
        if ($isEmptyEmail) {
            $email = $phone . '@phone.user';
        }

        // Unique validations before DB operation
        if ($email && $this->checkUserEmail($email)) {
            $request->validate([
                'email' => 'unique:users,email',
            ], [
                'email.unique' => 'Email already exists.',
            ]);
        }

        if ($this->checkUserPhone($phone) && !$this->checkUserExists($phone)) {
            $request->validate([
                'phone' => 'unique:users,phone',
            ], [
                'phone.unique' => 'Phone number already exists.',
            ]);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Start DB Transaction
        DB::beginTransaction();

        try {
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $request->name,
                    'phone' => $phone,
                    'email' => $email,
                    'password' => Hash::make($request->password),
                    'otp' => $otp,
                    'otp_expires_at' => now()->addMinutes(5),
                    'created_by' => 'self',
                ]);
            } else {
                $user->update([
                    'name' => $request->name,
                    'email' => $email,
                    'password' => Hash::make($request->password),
                    'otp' => $otp,
                    'otp_expires_at' => now()->addMinutes(5),
                ]);
            }

            // Send OTP via email (if provided)
            if (!$isEmptyEmail) {
                // Uncomment to enable email sending
                try {
                    Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
                    $message->to($user->email)->subject('Your OTP for Account Verification');
                });
                } catch (\Exception $e) {
                    // Mail sending failed, but continue with the process
                }
            }

            // TODO: Implement SMS sending for the phone number
            $message = "Your OTP is: $otp";
            HutchSmsUtility::sendSms($phone, $message);
            DB::commit();

            return response()->json([
                'success' => true,
                'otp' => $otp,
                'message' => 'OTP has been sent to your contact information.',
                'phone' => $phone,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {

        $request->type == 'email' ? $request->validate([
            'email' => 'required|email|exists:users,email'
        ]) : $request->validate([
            'phone' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!phoneNumberValidation($value)) {
                    $fail('Please enter a valid Sri Lankan phone number.');
                }
            }],
        ]);

        try{
            $type = $request->type;



            if($type == 'email'){
                $user = User::where('email', $request->email)->where('is_active', 1)->first();
            }else{
                $user = User::where('phone', $request->phone)->where('is_active', 1)->first();
            }

            if(!$user){
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 500);
            }

            $otp = rand(100000, 999999);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            if($type == 'email'){
                Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Your Nature\'s Virtue Verification Code');
                });
            }else{
                // TODO: Implement SMS sending for phone number
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your contact information.',
                'type' => $type,
                'phone' => $request->phone,
                'otp' => $otp
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 500);
        }


    }

    public function verifyRegistration(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if(!$user || $user->otp !== $request->otp ||  $user->otp_expires_at->isPast()){
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.'
            ], 500);
        }

        // Clear OTP and mark user as verified
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->phone_verified_at = now();
        $user->save();

        // Log in the user
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Account verified successfully.',
            'redirect' => route('home')
        ]);
    }


    /**
     * Check if user email exists
     * @param string $email
     * @return bool
     */
    private function checkUserEmail($email)
    {
        return User::where('email', $email) ->where('created_by', 'self')->exists();
    }

    /**
     * Check if user phone exists
     * @param string $phone
     * @return bool
     */
    private function checkUserPhone($phone)
    {
        return User::where('phone', $phone) ->where('created_by', 'self')->exists();
    }

    /**
     * Check if user exists
     * @param string $phone
     * @return bool
     */
    private function checkUserExists($phone)
    {
        $existingUser = User::where('phone', $phone)->whereNull('phone_verified_at')->first();

        if (!$existingUser) {
            return false;
        }

        $timeDifference = now()->diffInMinutes($existingUser->created_at);

        if ($timeDifference <= 10) {
            return true;
        }
        return false;
    }

    public function checkUserEmailExists($email)
    {
        return User::where('email', $email)->where('created_by', 'self')->exists();
    }
}
