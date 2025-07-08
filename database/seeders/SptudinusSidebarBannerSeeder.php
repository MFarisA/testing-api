<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SptudinusSidebarBannerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $data = [];

        for ($i = 1; $i <= 23; $i++) {
            $data[] = [
                // 'gambar' => $faker->imageUrl(640, 480, 'business', true, 'Banner'),
                'gambar' => '/image-testing/burung-perkasa.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('v2_sptudinus_sidebar_banner')->insert($data);
    }
}
