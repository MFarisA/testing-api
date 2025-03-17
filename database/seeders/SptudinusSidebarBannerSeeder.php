<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SptudinusSidebarBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('v2_sptudinus_sidebar_banner')->insert([
            [
                'gambar' => 'banner1.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gambar' => 'banner2.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gambar' => 'banner3.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
