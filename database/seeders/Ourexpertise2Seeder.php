<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class Ourexpertise2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $faker = Faker::create();

    $data = [];

    for ($i = 0; $i < 23; $i++) {
        $data[] = [
            'thumbnail' => '/uploads/expertise2/' . $faker->word . '.jpg',
            'judul' => ucfirst($faker->bs),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('v2_home_ourexpertise2')->insert($data);
}


}
