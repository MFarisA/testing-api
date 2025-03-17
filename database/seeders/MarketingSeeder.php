<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;


class MarketingSeeder extends Seeder
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
            'judul' => ucfirst($faker->catchPhrase()),
            // 'foto' => '/uploads/marketing/' . $faker->slug . '.jpg',
            'foto' => '/image-testing/burung-perkasa.jpg',
            'isi' => $faker->paragraph(3),
            'video' => 'https://youtube.com/embed/' . $faker->regexify('[A-Za-z0-9_-]{11}'),
            'user_id' => 1, // pastikan user_id 1 ada
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('tb_marketing')->insert($data);
}

}
