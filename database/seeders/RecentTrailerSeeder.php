<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecentTrailerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('v2_recenttrailer')->insert([
            [
                'judul' => 'Official Trailer - The Journey',
                'date' => '2025-03-01',
                'youtube_id' => 'a1b2c3d4e5f',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Teaser - Next Big Thing',
                'date' => '2025-02-15',
                'youtube_id' => 'z9y8x7w6v5t',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
