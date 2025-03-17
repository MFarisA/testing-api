<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Teknologi', 'slug' => 'teknologi', 'top_nav' => 1, 'urutan' => 1],
            ['nama' => 'Olahraga', 'slug' => 'olahraga', 'top_nav' => 1, 'urutan' => 2],
            ['nama' => 'Hiburan', 'slug' => 'hiburan', 'top_nav' => 0, 'urutan' => 3],
            ['nama' => 'Politik', 'slug' => 'politik', 'top_nav' => 0, 'urutan' => 4],
            ['nama' => 'Ekonomi', 'slug' => 'ekonomi', 'top_nav' => 1, 'urutan' => 5],
        ];

        foreach ($kategori as $item) {
            DB::table('tb_kategori')->updateOrInsert(
                ['slug' => Str::slug($item['slug'])],
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
