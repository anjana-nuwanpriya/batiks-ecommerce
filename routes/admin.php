<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\LogViewerController;


use App\Http\Controllers\Admin\FlashDealController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\OverseasOrderController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

// Admin login redirect
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::redirect('/', 'admin/dashboard');






    // Category
    Route::post('category/status/{category}', [CategoryController::class, 'categoryStatus'])->name('category.status');
    Route::post('category/featured/{category}', [CategoryController::class, 'categoryFeatured'])->name('category.featured');
    Route::post('category/delete/bulk', [CategoryController::class, 'destroyBulk'])->name('category.destroy.all');
    Route::resource('category', CategoryController::class);


    // Product
    Route::resource('product', ProductController::class);
    Route::post('product/status/{product}', [ProductController::class, 'productStatus'])->name('product.status');
    Route::post('product/featured/{product}', [ProductController::class, 'productFeatured'])->name('product.featured');
    Route::post('product/delete/bulk', [ProductController::class, 'destroyBulk'])->name('product.destroy.all');
    Route::post('product/update-order', [ProductController::class, 'updateOrder'])->name('product.update-order');
    Route::post('product/reset-sort-order', [ProductController::class, 'resetSortOrder'])->name('product.reset-sort-order');

    // Review
    Route::get('review', [ReviewController::class, 'index'])->name('review.index');
    Route::post('review/status/{review}', [ReviewController::class, 'reviewStatus'])->name('review.status');
    Route::post('review/show-home/{review}', [ReviewController::class, 'showHome'])->name('review.show.home');
    Route::get('review/show/{review}', [ReviewController::class, 'show'])->name('review.show');

    // Setting
    Route::get('shipping-setting', [SettingController::class, 'shippingSetting'])->name('shipping.setting');
    Route::get('payment-setting', [SettingController::class, 'paymentSetting'])->name('payment.setting');
    Route::get('site-setting', [SettingController::class, 'siteSetting'])->name('site.setting');
    Route::post('shipping-setting/update', [SettingController::class, 'shippingSettingUpdate'])->name('shipping.setting.update');
    Route::post('shipping-weights/update', [SettingController::class, 'shippingWeightUpdate'])->name('shipping.weight.update');
    Route::post('save-settings', [SettingController::class, 'saveSettings'])->name('save.settings');
    Route::post('save-general-settings', [SettingController::class, 'saveGeneralSettings'])->name('save.general.settings');
    Route::post('save-social-settings', [SettingController::class, 'saveSocialSettings'])->name('save.social.settings');
    Route::post('site-settings/update', [SettingController::class, 'siteSettingsUpdate'])->name('site.settings.update');
    Route::post('test-email', [SettingController::class, 'testEmail'])->name('test.email');
    Route::post('test-sms', [SettingController::class, 'testSms'])->name('test.sms');
    Route::get('sms-status-page', [SettingController::class, 'smsStatusPage'])->name('sms.status.page');
    Route::get('sms-status', [SettingController::class, 'smsStatus'])->name('sms.status');
    Route::get('sms-clear-tokens', [SettingController::class, 'clearSmsTokens'])->name('sms.clear.tokens');

    // Order
    Route::get('order', [OrderController::class, 'index'])->name('order.index');
    Route::get('order/{order}', [OrderController::class, 'show'])->name('admin.order.show');
    Route::put('order/{order}/status', [OrderController::class, 'updateOrderStatus'])->name('order.status.update');
    Route::put('order/{order}/delivery-status', [OrderController::class, 'updateDeliveryStatus'])->name('order.delivery.status.update');
    Route::put('order/{order}/shipping-address', [OrderController::class, 'updateShippingAddress'])->name('admin.order.update.shipping');
    Route::post('order/{order}/create-waybill', [OrderController::class, 'createWaybill'])->name('admin.order.create.waybill');

    Route::get('order/promptx/waybill/{order}', [OrderController::class, 'promptXWaybillDownload'])->name('admin.promptx.waybill');


    // Admin Orders
    Route::get('admin-orders', [OrderController::class, 'adminOrders'])->name('admin.orders.index');
    Route::get('admin-orders/create', [OrderController::class, 'createAdminOrder'])->name('admin.orders.create');
    Route::get('admin/product/variants', [OrderController::class, 'getProductVariants'])->name('admin.product.variants');
    Route::get('admin/product/search', [ProductController::class, 'search'])->name('admin.product.search');
    Route::get('admin/cities/search', [OrderController::class, 'searchCities'])->name('admin.cities.search');
    Route::post('admin/order/add-product', [OrderController::class, 'addProductToOrder'])->name('admin.order.add.product');
    Route::post('admin/order/store', [OrderController::class, 'storeAdminOrder'])->name('admin.order.store');
    Route::post('admin/order/calculate-shipping', [OrderController::class, 'calculateShippingCost'])->name('admin.order.calculate.shipping');

    // Reports
    Route::get('sales-report', [ReportController::class, 'salesReport'])->name('sales.report');
    Route::get('products-sales-report', [ReportController::class, 'productsSalesReport'])->name('products.sales.report');
    Route::get('customer-report', [ReportController::class, 'customerReport'])->name('customer.report');
    Route::get('products-stock-report', [ReportController::class, 'productsStockReport'])->name('products.stock.report');

    //Inquiries
    Route::get('product-inquiries', [InquiryController::class, 'index'])->name('product-inquiries.index');
    Route::get('product-inquiries/{inquiry}', [InquiryController::class, 'show'])->name('product-inquiries.show');
    Route::put('product-inquiries/{inquiry}', [InquiryController::class, 'update'])->name('product-inquiries.update');
    Route::delete('product-inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('product-inquiries.destroy');

    //Customers
    Route::get('customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::post('customer/store', [CustomerController::class, 'store'])->name('admin.customer.store');
    Route::get('customer/address', [CustomerController::class, 'getAddress'])->name('admin.customer.address');
    Route::post('customer/address/store', [CustomerController::class, 'storeAddress'])->name('admin.customer.store.address');
    Route::post('customer/address/delete', [CustomerController::class, 'deleteAddress'])->name('admin.customer.delete.address');
    Route::post('customer/{customer}/activate', [CustomerController::class, 'activateCustomer'])->name('admin.customer.activate');
    Route::delete('customer/{customer}', [CustomerController::class, 'destroy'])->name('admin.customer.destroy');

    //Transactions
    Route::get('transaction', [TransactionController::class, 'index'])->name('transaction.index');


    Route::get('receipt/{orderId}', [OrderController::class, 'receipt'])->name('admin.receipt');

    //Users
    Route::resource('role', RoleController::class);
    Route::resource('staff', StaffController::class);
    Route::put('update-account-info', [StaffController::class, 'updateAccountInfo'])->name('admin.update.account.info');

    //Pages
    Route::resource('main-banner', BannerController::class);
    Route::post('main-banner/status/{banner}', [BannerController::class, 'bannerStatus'])->name('main-banner.status');
    Route::post('main-banner/update-order', [BannerController::class, 'updateOrder'])->name('main-banner.update-order');

    //Blogs
    Route::resource('blog', BlogController::class);
    Route::post('blog/status/{blog}', [BlogController::class, 'blogStatus'])->name('blog.status');

    //Account
    Route::get('account/settings', [StaffController::class, 'changeAccountInfo'])->name('account.settings');

    //Pages
    Route::get('home-page', [PageController::class, 'homePage'])->name('home.page');
    Route::get('about-page', [PageController::class, 'aboutPage'])->name('about.page');
    Route::post('about-section/{sectionName}', [PageController::class, 'updateAboutSection'])->name('about.section.update');
    Route::get('about-section/{sectionName}', [PageController::class, 'getAboutSection'])->name('about.section.get');

    //Flash Deals
    Route::get('flash-deal', [FlashDealController::class, 'index'])->name('flash.deal.index');
    Route::get('flash-deal/create', [FlashDealController::class, 'create'])->name('flash.deal.create');
    Route::post('flash-deal/store', [FlashDealController::class, 'store'])->name('flash.deal.store');
    Route::get('flash-deal/{flashDeal}', [FlashDealController::class, 'show'])->name('flash.deal.show');
    Route::put('flash-deal/{flashDeal}', [FlashDealController::class, 'update'])->name('flash.deal.update');
    Route::delete('flash-deal/{flashDeal}', [FlashDealController::class, 'destroy'])->name('flash.deal.destroy');
    Route::get('flash-deal/{flashDeal}/edit', [FlashDealController::class, 'edit'])->name('flash.deal.edit');
    Route::post('flash-deal/status/{flashDeal}', [FlashDealController::class, 'flashDealStatus'])->name('flash.deal.status');

    // Centralized Activity Logs (place this before other routes)
    Route::get('activity-logs', [App\Http\Controllers\Admin\AdminActivityLogController::class, 'index'])->name('admin.activity-logs.index');
    Route::get('activity-logs/stats', [App\Http\Controllers\Admin\AdminActivityLogController::class, 'getStats'])->name('admin.activity-logs.stats');
    Route::get('activity-logs/export', [App\Http\Controllers\Admin\AdminActivityLogController::class, 'export'])->name('admin.activity-logs.export');
    Route::get('activity-logs/{activity}', [App\Http\Controllers\Admin\AdminActivityLogController::class, 'show'])->name('admin.activity-logs.show');
    Route::post('activity-logs/cleanup', [App\Http\Controllers\Admin\AdminActivityLogController::class, 'cleanup'])->name('admin.activity-logs.cleanup');

    // Overseas Orders
    Route::resource('overseas-orders', OverseasOrderController::class)->names([
        'index' => 'admin.overseas-orders.index',
        'create' => 'admin.overseas-orders.create',
        'store' => 'admin.overseas-orders.store',
        'show' => 'admin.overseas-orders.show',
        'edit' => 'admin.overseas-orders.edit',
        'update' => 'admin.overseas-orders.update',
        'destroy' => 'admin.overseas-orders.destroy',
    ]);
    Route::get('overseas-orders/product/variants', [OverseasOrderController::class, 'getProductVariants'])->name('admin.overseas-orders.product.variants');
    Route::post('overseas-orders/customer/create', [OverseasOrderController::class, 'createCustomer'])->name('admin.overseas-orders.customer.create');

    // Maintenance Mode Management
    Route::get('maintenance', [MaintenanceController::class, 'index'])->name('admin.maintenance.index');
    Route::post('maintenance/enable', [MaintenanceController::class, 'enable'])->name('admin.maintenance.enable');
    Route::post('maintenance/disable', [MaintenanceController::class, 'disable'])->name('admin.maintenance.disable');
    Route::get('maintenance/status', [MaintenanceController::class, 'status'])->name('admin.maintenance.status');

    // Log Viewer
    Route::get('logs', [App\Http\Controllers\Admin\LogViewerController::class, 'index'])->name('admin.logs.index');
    Route::get('logs/download', [App\Http\Controllers\Admin\LogViewerController::class, 'download'])->name('admin.logs.download');
    Route::any('logs/clear', [App\Http\Controllers\Admin\LogViewerController::class, 'clear'])->name('admin.logs.clear');
    Route::post('logs/delete', [App\Http\Controllers\Admin\LogViewerController::class, 'delete'])->name('admin.logs.delete');


});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('invoice/{order}', [OrderController::class, 'invoice'])->name('admin.invoice');
});

// Cache Clear
Route::get('clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    toast('Cache cleared successfully!', 'success');
    return redirect()->back();
})->name('cache.clear');


// Storage Link
Route::get('storage-link', function () {
    Artisan::call('storage:link');
    toast('Storage linked successfully!', 'success');
    return redirect()->back();
})->name('storage.link');


// Permission Seeder
Route::get('permission-seeder', function () {
    if (config('app.debug')) {
        Artisan::call('db:seed', ['--class' => 'PermissionSeeder']);

        toast('Permissions seeded successfully!', 'success');
        return redirect()->back();
    }
    abort(404);
})->name('permission.seeder');

// Super Admin Permission Sync
Route::get('sync-super-admin-permissions', function () {
    if (config('app.debug')) {
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        $allPermissions = \Spatie\Permission\Models\Permission::all();

        $superAdminRole->syncPermissions($allPermissions);

        toast('Super Admin permissions synced successfully! (' . $allPermissions->count() . ' permissions assigned)', 'success');
        return redirect()->back();
    }
    abort(404);
})->name('sync.super.admin.permissions');



// Waybill Retry System Routes
Route::get('process-waybill-retries', function () {
    try {
        Artisan::call('waybills:process-retries');
        $output = Artisan::output();

        toast('Waybill retries processed successfully!', 'success');
        return redirect()->back()->with('artisan_output', $output);
    } catch (Exception $e) {
        toast('Error processing waybill retries: ' . $e->getMessage(), 'error');
        return redirect()->back();
    }
})->name('process.waybill.retries');

Route::get('create-missing-waybills', function () {
    try {
        Artisan::call('waybills:create-missing');
        $output = Artisan::output();

        toast('Missing waybills creation processed successfully!', 'success');
        return redirect()->back()->with('artisan_output', $output);
    } catch (Exception $e) {
        toast('Error creating missing waybills: ' . $e->getMessage(), 'error');
        return redirect()->back();
    }
})->name('create.missing.waybills');




