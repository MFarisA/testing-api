<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OurexpertiseSeeder extends Seeder
{
    public function run(): void
{
    $faker = Faker::create();

    $data = [];

    for ($i = 0; $i < 23; $i++) {
        $data[] = [
            'thumbnail' => '/uploads/expertise/' . $faker->word . '.jpg',
            'judul' => ucfirst($faker->catchPhrase),
            'deskripsi' => $faker->sentence(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('v2_home_ourexpertise1')->insert($data);
}


}
