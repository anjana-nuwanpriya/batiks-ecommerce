<?php

use App\Http\Controllers\AuthWeb\LoginController;
use App\Http\Controllers\AuthWeb\SocialController;
use App\Http\Controllers\AuthWeb\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



//User login
Route::get('/user/login', [LoginController::class, 'showLoginForm'])->name('user.login');
Route::post('/user/send-otp', [LoginController::class, 'sendOtp'])->name('user.sendOtp');
Route::get('/verify-otp', [LoginController::class,'showVerifyForm'])->name('user.verify.form');
Route::post('/user/verify-otp', [LoginController::class, 'verifyOtp'])->name('user.verifyOtp');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/user/login', [LoginController::class, 'login'])->name('user.login');

//User Register
Route::get('/user/register', [RegisterController::class, 'showRegisterForm'])->name('user.register');
Route::post('/user/register', [RegisterController::class, 'register'])->name('user.register');
Route::post('/user/verify-registration', [RegisterController::class, 'verifyRegistration'])->name('user.verify.registration');

//User Forgot Password
Route::get('/user/forgot-password', [RegisterController::class, 'showForgotPasswordForm'])->name('user.forgot.password');
Route::post('/user/forgot-password', [RegisterController::class, 'forgotPassword'])->name('user.forgot.password');

Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialController::class, 'handleGoogleCallback']);

Route::get('auth/facebook', [SocialController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('auth/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

// Phone Verification Routes
Route::post('/phone/send-otp', [PhoneVerificationController::class, 'sendOTP'])->name('phone.send.otp');
Route::post('/phone/verify-otp', [PhoneVerificationController::class, 'verifyOTP'])->name('phone.verify.otp');
Route::post('/phone/check-verification', [PhoneVerificationController::class, 'checkVerification'])->name('phone.check.verification');

// City search API
Route::get('/api/cities/search', [CheckoutController::class, 'searchCities'])->name('api.cities.search');


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

Route::any('uploader', [MediaController::class, 'upload'])->name('upload');


// Page Routes
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('about-us', [HomeController::class, 'about'])->name('about');
Route::get('blog/{slug}', [HomeController::class, 'blog'])->name('blog');
Route::post('/contact/store', [HomeController::class, 'contactStore'])->name('contact.store');


// Product Routes
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('sf.product.show');

// Search
Route::get('/products/{category?}', [SearchController::class, 'search'])->name('sf.products.list');
Route::get('/suggestions', [SearchController::class, 'suggestions'])->name('sf.product.suggestions');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Checkout Routes
Route::group(['prefix' => 'cart'], function () {
    Route::get('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/variant-availability', [CartController::class, 'variantAvailability'])->name('cart.variant.availability');
    Route::post('/remove-from-cart', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/update-cart', [CartController::class, 'updateCart'])->name('cart.update');
    Route::post('/clear-cart', [CartController::class, 'clearCart'])->name('cart.clear');
});

Route::group(['prefix' => 'checkout'] , function () {
    Route::get('/', [CheckoutController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('cart.checkout.process');
    Route::get('/calculate-shipping-cost', [CheckoutController::class, 'getShippingCostForSummary'])->name('cart.shipping.cost');
    Route::get('/order-complete/{order}', [CheckoutController::class, 'orderComplete'])->name('cart.order.complete');
});

// Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

//User Dashboard
Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/orders', [UserDashboardController::class, 'viewOrders'])->name('user.order-list');
    Route::get('/order/{order}', [UserDashboardController::class, 'viewOrder'])->name('user.order.view');
    Route::get('/wishlist', [UserDashboardController::class, 'wishlist'])->name('user.wishlist');
});

// Additional User Dashboard Routes
Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('/manage-account', [UserDashboardController::class, 'manageAccount'])->name('user.manage.account');
    Route::post('/store-address', [UserDashboardController::class, 'storeAddress'])->name('user.store.address');
    Route::post('/set-default-address', [UserDashboardController::class, 'setDefaultAddress'])->name('user.set.default.address');
    Route::post('/delete-address', [UserDashboardController::class, 'deleteAddress'])->name('user.delete.address');
    Route::post('/order/{order}/upload-slip', [UserDashboardController::class, 'uploadSlip'])->name('user.order.upload-slip');
    Route::post('/update-profile', [UserDashboardController::class, 'updateProfile'])->name('user.update.profile');
    Route::post('/edit-address', [UserDashboardController::class, 'editAddressView'])->name('user.edit.address.view');
    Route::put('/update-address/{address}', [UserDashboardController::class, 'updateAddress'])->name('user.update.address');
});

// Product Review
Route::middleware('auth')->post('/product/review/store', [ProductController::class, 'storeReview'])->name('sf.product.review.store');

// Inquiry
Route::post('/inquiry', [HomeController::class, 'storeInquiry'])->name('inquiry.store');
Route::get('/product/{product}/variants', [ProductController::class, 'getVariants'])->name('product.variants');


// Wishlist
Route::post('/wishlist/add', [ProductController::class, 'addToWishlist'])->name('wishlist.add');
Route::post('/wishlist/remove', [ProductController::class, 'removeFromWishlist'])->name('wishlist.remove');
//Pages
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/return-policy', [HomeController::class, 'returnPolicy'])->name('return-policy');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');




// Payment Routes
Route::any('payhere/payment/return', [PaymentController::class, 'payhereReturn'])->name('payment.payhere.return');
Route::any('payhere/payment/cancel', [PaymentController::class, 'payhereCancel'])->name('payment.payhere.cancel');
Route::any('payhere/payment/notify', [PaymentController::class, 'payhereNotify'])->name('payment.payhere.notify');


// Tracking Routes
Route::get('/track-order', [TrackingController::class, 'index'])->name('tracking.index');
Route::get('/order/track-order', [TrackingController::class, 'track'])->name('tracking.track');
Route::get('/track-order/{orderId}', [TrackingController::class, 'orderTracking'])->name('tracking.order');
Route::post('/tracking/status', [TrackingController::class, 'getTrackingStatus'])->name('tracking.status');


