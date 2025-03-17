<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RecentTrailerSeeder extends Seeder
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
            'judul' => $faker->randomElement([
                'Official Trailer', 'Teaser', 'Behind The Scenes', 'Featurette', 'Sneak Peek'
            ]) . ' - ' . ucfirst($faker->words(3, true)),
            'date' => $faker->date('Y-m-d', '2025-12-31'),
            'youtube_id' => $faker->regexify('[a-zA-Z0-9_-]{11}'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('v2_recenttrailer')->insert($data);
}
}
