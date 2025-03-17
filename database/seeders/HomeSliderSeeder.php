<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomeSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('v2_home_slider')->insert([
            [
                'judul' => 'Welcome to Our Website',
                'sub_judul' => 'Discover our services and solutions',
                'gambar' => '/uploads/sliders/slider1.jpg',
                'urutan' => 1,
                'url' => 'https://example.com/services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Grow Your Business',
                'sub_judul' => 'We help you reach your goals',
                'gambar' => '/uploads/sliders/slider2.jpg',
                'urutan' => 2,
                'url' => 'https://example.com/about',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Join Our Community',
                'sub_judul' => 'Be part of something bigger',
                'gambar' => '/uploads/sliders/slider3.jpg',
                'urutan' => 3,
                'url' => 'https://example.com/contact',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
