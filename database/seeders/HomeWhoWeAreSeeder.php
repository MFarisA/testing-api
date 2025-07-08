<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class HomeWhoWeAreSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $data = [];

        for ($i = 1; $i <= 23; $i++) {
            $data[] = [
                'judul' => $faker->sentence(3),
                'deskripsi' => $faker->paragraph(3),
                // 'gambar' => '/uploads/whoweare/whoweare' . $i . '.jpg',
                'gambar' => '/image-testing/burung-perkasa.jpg',
                'motto1' => $faker->word(),
                'motto2' => $faker->word(),
                'motto3' => $faker->word(),
                'motto1sub' => $faker->sentence(),
                'motto2sub' => $faker->sentence(),
                'motto3sub' => $faker->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('v2_home_whoweare')->insert($data);
    }


}
