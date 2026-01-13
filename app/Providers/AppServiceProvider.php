<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Anhskohbo\NoCaptcha\NoCaptcha;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Default string length
        Schema::defaultStringLength(191);

        //Parent Category
        View::composer(['frontend.layouts.header', 'frontend.layouts.footer'], function ($view) {
            $parentCategories = Category::whereNull('parent_id')->where('status', true)->where('featured', true)->get();
            $view->with('parentCategories', $parentCategories);
        });

        View::composer(['frontend.layouts.footer'], function ($view) {
            $products = Product::where('is_active', true)->get();
            $view->with('products', $products);
        });

        Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
            $captcha = app('captcha');
            return $captcha->verifyResponse($value, request()->ip());
        });

    }
}
