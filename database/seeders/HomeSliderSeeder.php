<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class HomeSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $data = [
            [
                'judul' => 'Welcome to Our Website',
                'sub_judul' => 'Discover our services and solutions',
                // 'gambar' => '/uploads/sliders/slider1.jpg',
                'gambar' => '/image-testing/burung-perkasa.jpg',
                'urutan' => 1,
                'url' => 'https://example.com/services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Grow Your Business',
                'sub_judul' => 'We help you reach your goals',
                // 'gambar' => '/uploads/sliders/slider2.jpg',
                'gambar' => '/image-testing/burung-perkasa.jpg',
                'urutan' => 2,
                'url' => 'https://example.com/about',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Join Our Community',
                'sub_judul' => 'Be part of something bigger',
                // 'gambar' => '/uploads/sliders/slider3.jpg',
                'gambar' => '/image-testing/burung-perkasa.jpg',
                'urutan' => 3,
                'url' => 'https://example.com/contact',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Tambahkan 20 data dummy dengan Faker
        for ($i = 4; $i <= 23; $i++) {
            $data[] = [
                'judul' => $faker->catchPhrase(),
                'sub_judul' => $faker->sentence(6),
                // 'gambar' => '/uploads/sliders/slider' . $i . '.jpg', // kamu bisa ganti ke $faker->imageUrl() kalau mau URL gambar dummy
                'gambar' => '/image-testing/burung-perkasa.jpg',
                'urutan' => $i,
                'url' => $faker->url(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('v2_home_slider')->insert($data);
    }
}
