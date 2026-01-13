<?php

namespace Database\Seeders;

use App\Models\AboutPage;
use Illuminate\Database\Seeder;

class AboutPageSeeder extends Seeder
{
    public function run()
    {
        $sections = [
            [
                'section_name' => 'hero',
                'content' => [
                    'badge_text' => 'About NatureVirtue.lk',
                    'main_title' => 'From Nature to Your Home',
                    'highlight_text' => 'Pure, Natural, Trusted',
                    'description' => "At NatureVirtue.lk, we specialize in premium dehydrated foods and natural herbal products made in the heart of Sri Lanka. Born in the coastal village of Habaraduwa, our brand began with a simple goal: to make clean, healthy living more accessible to everyone.\n\nToday, we proudly produce our own range of products from sun-dried fruits and vegetables to herbal teas, wellness capsules, and superfoods. Every item is made using gentle dehydration methods that preserve maximum flavor, color, and nutrition with no added chemicals, preservatives, or artificial ingredients.\n\nWe partner with local farmers to source responsibly and support sustainable agriculture. Our facilities are ISO 22000:2018 and HACCP certified, ensuring global standards of food safety and quality control.\n\nWhether you're seeking nutritious snacks, natural remedies, or health-boosting herbs, NatureVirtue.lk delivers freshness, purity, and goodness right to your doorstep."
                ],
                'is_active' => true
            ],
            [
                'section_name' => 'certifications',
                'content' => [
                    'title' => 'Quality You Can Trust',
                    'subtitle' => 'We maintain the highest standards of quality and safety through internationally recognized certifications'
                ],
                'is_active' => true
            ],
            [
                'section_name' => 'vision_mission',
                'content' => [
                    'section_title' => 'Our Vision & Mission',
                    'section_subtitle' => 'Driving innovation in natural food solutions to create a healthier, more sustainable world',
                    'vision_title' => 'Our Vision',
                    'vision_content' => 'To be a global leader in nourishing communities through innovative, sustainable, and delicious dehydrated food solutions, ensuring a healthier and more resilient world.',
                    'mission_title' => 'Our Mission',
                    'mission_content' => 'To provide nutrient-rich, convenient, and sustainable food solutions, using innovative dehydration processes, to nourish and inspire a thriving global community.'
                ],
                'is_active' => true
            ],
            [
                'section_name' => 'counter',
                'content' => [
                    'years_count' => 5,
                    'years_label' => 'Years of Experience',
                    'customers_count' => 100,
                    'customers_label' => 'Happy Customer',
                    'products_count' => 200,
                    'products_label' => 'Products',
                    'awards_count' => 5,
                    'awards_label' => 'Award Winning'
                ],
                'is_active' => true
            ],
            [
                'section_name' => 'why_choose',
                'content' => [
                    'main_title' => '100% Trusted Source for Dehydrated Foods & Herbal Products',
                    'description' => 'Experience the pure goodness of nature with our carefully selected range of dehydrated foods and herbal products. Whether you\'re looking for healthy snacks, wellness herbs, or convenient nutrition—Nature\'s Virtue delivers quality you can trust.',
                    'features' => [
                        [
                            'title' => 'Premium Quality Products',
                            'description' => 'Naturally preserved and carefully crafted for long-lasting freshness and nutrition.',
                            'icon' => 'food.png'
                        ],
                        [
                            'title' => '24/7 Customer Support',
                            'description' => 'Always here to help—reach us anytime with your questions or concerns.',
                            'icon' => 'help.png'
                        ],
                        [
                            'title' => 'Free & Fast Shipping',
                            'description' => 'Enjoy doorstep delivery with exclusive discounts and offers.',
                            'icon' => 'feedback.png'
                        ],
                        [
                            'title' => '100% Secure Payments',
                            'description' => 'Shop confidently with our safe and encrypted checkout process.',
                            'icon' => 'secure.png'
                        ],
                        [
                            'title' => 'Free Shipping',
                            'description' => 'Free shipping with discount',
                            'icon' => 'lorry.png'
                        ],
                        [
                            'title' => '100% Organic Food',
                            'description' => '100% healthy & fresh food',
                            'icon' => 'products.png'
                        ]
                    ]
                ],
                'is_active' => true
            ]
        ];

        foreach ($sections as $section) {
            AboutPage::updateOrCreate(
                ['section_name' => $section['section_name']],
                $section
            );
        }
    }
}
