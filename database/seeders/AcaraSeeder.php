<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class AcaraSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $data = [
            [
                'nama_acara' => 'Acara Ulang Tahun Perusahaan',
                'thumbnail_acara' => '/uploads/acara/ulang-tahun.jpg',
                'description' => 'Perayaan ulang tahun perusahaan ke-10 yang meriah dan penuh kejutan.',
                'path' => '/events/ulang-tahun',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_acara' => 'Webinar Digital Marketing',
                'thumbnail_acara' => '/uploads/acara/webinar.jpg',
                'description' => 'Webinar tentang strategi digital marketing di era modern.',
                'path' => '/events/webinar-digital-marketing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_acara' => 'Workshop Desain Grafis',
                'thumbnail_acara' => '/uploads/acara/workshop.jpg',
                'description' => 'Pelatihan intensif tentang desain grafis menggunakan tools terkini.',
                'path' => '/events/workshop-desain',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        for ($i = 0; $i < 20; $i++) {
            $namaAcara = $faker->sentence(3);
            $slug = Str::slug($namaAcara);

            $data[] = [
                'nama_acara' => $namaAcara,
                'thumbnail_acara' => $faker->imageUrl(640, 480, 'events', true, 'acara'), 
                'description' => $faker->paragraph,
                'path' => '/events/' . $slug,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert seluruh data
        DB::table('tb_acara')->insert($data);
    }

}
