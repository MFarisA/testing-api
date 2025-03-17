<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SptdinusSlidesTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('v2_sptdinus_slides_title')->insert([
            [
                'judul' => 'Program Unggulan',
                'urutan' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Event Spesial',
                'urutan' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Pengumuman Penting',
                'urutan' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
