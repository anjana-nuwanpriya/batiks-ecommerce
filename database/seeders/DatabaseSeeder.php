<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed permissions first
        $this->call([
            PermissionSeeder::class,
            CitiesSeeder::class,
        ]);

        // Create a test user if none exists
        if (User::count() === 0) {
            User::factory(10)->create();
        }

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Create Categories
        $vegetables = Category::create([
            'name' => 'Vegetables',
            'slug' => 'vegetables',
            'description' => 'Fresh vegetables from organic farms',
            'image' => 'images/categories/vegetables.jpg'
        ]);

        $fruits = Category::create([
            'name' => 'Fruits',
            'slug' => 'fruits',
            'description' => 'Fresh fruits from local farms',
            'image' => 'images/categories/fruits.jpg'
        ]);

        // Create Products
        Product::create([
            'name' => 'Chinese Cabbage',
            'description' => 'Fresh organic Chinese cabbage from local farms',
            'long_description' => 'Our Chinese cabbage is grown using organic farming methods, ensuring the highest quality and nutritional value. Perfect for stir-fries, salads, and traditional Asian dishes.',
            'price' => 17.28,
            'original_price' => 48.00,
            'image' => 'images/products/chinese-cabbage.jpg',
            'gallery' => [
                'images/products/chinese-cabbage-1.jpg',
                'images/products/chinese-cabbage-2.jpg',
                'images/products/chinese-cabbage-3.jpg'
            ],
            'category_id' => $vegetables->id,
            'rating' => 4.5,
            'reviews_count' => 4
        ]);

        Product::create([
            'name' => 'Green Apple',
            'description' => 'Fresh green apples from organic orchards',
            'long_description' => 'Our green apples are handpicked from organic orchards. They are crisp, juicy, and perfect for snacking or baking.',
            'price' => 14.99,
            'original_price' => 29.99,
            'image' => 'images/products/green-apple.jpg',
            'category_id' => $fruits->id,
            'rating' => 5,
            'reviews_count' => 8
        ]);

        Product::create([
            'name' => 'Fresh Cauliflower',
            'description' => 'Organic cauliflower from local farms',
            'long_description' => 'Our cauliflower is grown using sustainable farming practices. Perfect for roasting, steaming, or making cauliflower rice.',
            'price' => 14.99,
            'image' => 'images/products/cauliflower.jpg',
            'category_id' => $vegetables->id,
            'rating' => 4.8,
            'reviews_count' => 6
        ]);

        Product::create([
            'name' => 'Green Capsicum',
            'description' => 'Fresh organic green bell peppers',
            'long_description' => 'Our green bell peppers are grown organically and harvested at peak ripeness. They are crisp, flavorful, and perfect for salads, stir-fries, or stuffing.',
            'price' => 14.99,
            'image' => 'images/products/capsicum.jpg',
            'category_id' => $vegetables->id,
            'rating' => 4.7,
            'reviews_count' => 12
        ]);

        Product::create([
            'name' => 'Ladies Finger',
            'description' => 'Fresh organic okra/ladies finger',
            'long_description' => 'Our ladies finger (okra) is grown using organic farming methods. Perfect for traditional dishes and stir-fries.',
            'price' => 14.99,
            'image' => 'images/products/okra.jpg',
            'category_id' => $vegetables->id,
            'rating' => 4.6,
            'reviews_count' => 5
        ]);

        // Seed orders after products and users are created
        $this->call([
            OrderSeeder::class,
        ]);
    }
}
