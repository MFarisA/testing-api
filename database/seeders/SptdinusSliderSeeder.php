<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SptdinusSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('v2_sptdinus_slider')->insert([
            [
                'id_slides_title' => 1,
                'thumbnail' => 'slider1.jpg',
                'thumbnail_hover' => 'slider1_hover.jpg',
                'teks' => 'SPTDINUS Event',
                'link' => 'https://example.com/event-1',
                'deskripsi' => 'Deskripsi lengkap untuk slider pertama.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_slides_title' => 2,
                'thumbnail' => 'slider2.jpg',
                'thumbnail_hover' => 'slider2_hover.jpg',
                'teks' => 'SPTDINUS Announcement',
                'link' => 'https://example.com/announcement',
                'deskripsi' => 'Deskripsi lengkap untuk slider kedua.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
