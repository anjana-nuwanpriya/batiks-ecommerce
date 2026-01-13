<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Remove all previous permissions
        // Delete all permissions and their related model_has_permissions and role_has_permissions
        DB::table('model_has_permissions')->delete();
        DB::table('role_has_permissions')->delete();
        Permission::query()->delete();

        // Create permissions
        // Grouped permissions by module with 'section' key

        $permissions = [
            [
                'section' => 'Categories',
                'permissions' => [
                    'view_categories',
                    'create_category',
                    'edit_category',
                    'delete_category',
                ],
            ],
            [
                'section' => 'Products',
                'permissions' => [
                    'view_products',
                    'create_product',
                    'edit_product',
                    'delete_product',
                ],
            ],
            [
                'section' => 'Reviews',
                'permissions' => [
                    'view_reviews',
                    'manage_reviews',
                ],
            ],
            [
                'section' => 'Sales',
                'permissions' => [
                    'view_sales',
                    'create_order',
                    'manage_orders',
                    'view_orders',
                    'view_inquiries',
                    'overseas_orders',
                ],
            ],
            [
                'section' => 'Flash Deals',
                'permissions' => [
                    'view_flash_deals',
                ],
            ],
            [
                'section' => 'Blog',
                'permissions' => [
                    'view_blog',
                    'create_blog',
                    'edit_blog',
                    'delete_blog',
                ],
            ],
            [
                'section' => 'Pages',
                'permissions' => [
                    'view_pages',
                    'view_home_pages',
                    'view_about_pages',
                    'view_main_banners',
                ],
            ],
            [
                'section' => 'Users',
                'permissions' => [
                    'view_users',
                    'view_customers',
                    'view_staff',
                    'view_permissions',
                ],
            ],
            [
                'section' => 'Reports',
                'permissions' => [
                    'view_reports',
                    'view_sales_report',
                    'view_products_stock_report',
                    'view_customers_report',
                ],
            ],
            [
                'section' => 'Settings',
                'permissions' => [
                    'view_settings',
                ],
            ],
        ];

        foreach ($permissions as $group) {
            foreach ($group['permissions'] as $permission) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => 'web',
                    'section' => $group['section'],
                ]);
            }
        }
    }
}
