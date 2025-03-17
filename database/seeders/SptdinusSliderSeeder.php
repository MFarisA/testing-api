<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SptdinusSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $data = [];

        for ($i = 1; $i <= 23; $i++) {
            $data[] = [
                'id_slides_title' => $faker->numberBetween(1, 5),
                'thumbnail' => 'slider' . $i . '.jpg',
                'thumbnail_hover' => 'slider' . $i . '_hover.jpg',
                'teks' => 'SPTDINUS ' . $faker->randomElement(['Event', 'Announcement', 'News', 'Update']),
                'link' => $faker->url,
                'deskripsi' => $faker->sentence(8),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('v2_sptdinus_slider')->insert($data);
    }
}
