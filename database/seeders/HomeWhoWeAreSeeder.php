<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomeWhoWeAreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('v2_home_whoweare')->insert([
            [
                'judul' => 'Who We Are',
                'deskripsi' => 'We are a leading company in providing top-notch solutions for businesses worldwide. Our team is dedicated to delivering excellence and innovation.',
                'gambar' => '/uploads/whoweare/whoweare1.jpg',
                'motto1' => 'Innovation',
                'motto2' => 'Commitment',
                'motto3' => 'Excellence',
                'motto1sub' => 'We innovate to create better solutions.',
                'motto2sub' => 'We are committed to our clients’ success.',
                'motto3sub' => 'We strive for excellence in everything we do.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

}
