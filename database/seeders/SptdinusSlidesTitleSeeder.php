<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SptdinusSlidesTitleSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $data = [];

        for ($i = 1; $i <= 23; $i++) {
            $data[] = [
                'judul' => $faker->catchPhrase(),
                'urutan' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('v2_sptdinus_slides_title')->insert($data);
    }
}
