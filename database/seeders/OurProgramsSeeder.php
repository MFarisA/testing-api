<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OurProgramsSeeder extends Seeder
{
    public function run(): void
{
    $faker = Faker::create();

    $data = [];

    for ($i = 1; $i <= 23; $i++) {
        $data[] = [
            'thumbnail' => '/uploads/programs/' . $faker->word . '.jpg',
            'judul' => ucfirst($faker->bs), 
            'deskripsi' => $faker->sentence(12),
            'link' => '/programs/' . $faker->slug,
            'urutan' => $i,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('v2_our_programs')->insert($data);
}

}
