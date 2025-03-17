<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class MarketingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_marketing')->insert([
            'judul' => 'Promo Besar-Besaran Awal Tahun',
            'foto' => '/uploads/marketing/promo-awal-tahun.jpg',
            'isi' => 'Kami memberikan diskon besar hingga 70% untuk semua produk selama bulan ini.',
            'video' => 'https://youtube.com/embed/dummy-video-id',
            'user_id' => 1, // pastikan user id 1 ada di tabel users
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
