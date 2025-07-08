<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProgramSeeder extends Seeder
{
    public function run(): void
{
    $faker = Faker::create();
    $data = [];

    for ($i = 1; $i <= 23; $i++) {
        $data[] = [
            'judul' => 'Program ' . ucfirst($faker->word),
            'video' => '/videos/' . $faker->slug . '.mp4',
            // 'thumbnail' => '/uploads/programs/' . $faker->slug . '-thumbnail.jpg',
            'thumbnail' => '/image-testing/burung-perkasa.jpg',
            'deskripsi' => $faker->paragraph(2),
            'deskripsi_pendek' => $faker->sentence,
            'id_acara' => $faker->numberBetween(1, 5),
            'tanggal' => $faker->date('Y-m-d', '2025-12-31'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('tb_program')->insert($data);
}

}
