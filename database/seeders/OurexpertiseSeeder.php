<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OurexpertiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('v2_home_ourexpertise1')->insert([
            [
                'thumbnail' => '/uploads/expertise/design.jpg',
                'judul' => 'Creative Design',
                'deskripsi' => 'Kami menyediakan solusi desain kreatif untuk kebutuhan bisnis Anda.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thumbnail' => '/uploads/expertise/marketing.jpg',
                'judul' => 'Digital Marketing',
                'deskripsi' => 'Meningkatkan brand awareness dan penjualan melalui strategi digital.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thumbnail' => '/uploads/expertise/consulting.jpg',
                'judul' => 'Business Consulting',
                'deskripsi' => 'Konsultasi bisnis profesional untuk memaksimalkan potensi perusahaan Anda.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
