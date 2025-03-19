<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $kategori = [];

        // Generate 23 kategori dummy dari Faker
        for ($i = 1; $i <= 23; $i++) {
            $namaKategori = ucfirst($faker->unique()->word());
            $kategori[] = [
                'nama'      => $namaKategori,
                'slug'      => Str::slug($namaKategori),
                'top_nav'   => $faker->boolean(), // boolean true/false
                'urutan'    => $i,
                'created_at'=> now(),
                'updated_at'=> now(),
            ];
        }

        // Masukkan ke DB
        foreach ($kategori as $item) {
            DB::table('tb_kategori')->updateOrInsert(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
