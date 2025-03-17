<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class KategoriSeeder extends Seeder
{
    public function run(): void
{
    $faker = Faker::create();

    // Data default (5 kategori)
    $kategori = [
        ['nama' => 'Teknologi', 'slug' => 'teknologi', 'top_nav' => 1, 'urutan' => 1],
        ['nama' => 'Olahraga', 'slug' => 'olahraga', 'top_nav' => 1, 'urutan' => 2],
        ['nama' => 'Hiburan', 'slug' => 'hiburan', 'top_nav' => 0, 'urutan' => 3],
        ['nama' => 'Politik', 'slug' => 'politik', 'top_nav' => 0, 'urutan' => 4],
        ['nama' => 'Ekonomi', 'slug' => 'ekonomi', 'top_nav' => 1, 'urutan' => 5],
    ];

    // Generate tambahan 18 kategori dummy dari Faker (total jadi 23)
    for ($i = 6; $i <= 23; $i++) {
        $namaKategori = $faker->unique()->word();
        $kategori[] = [
            'nama' => ucfirst($namaKategori),
            'slug' => Str::slug($namaKategori),
            'top_nav' => $faker->boolean(),
            'urutan' => $i,
        ];
    }

    // Simpan ke database
    foreach ($kategori as $item) {
        DB::table('tb_kategori')->updateOrInsert(
            ['slug' => $item['slug']],
            [
                'nama' => $item['nama'],
                'top_nav' => $item['top_nav'],
                'urutan' => $item['urutan'],
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}

}
