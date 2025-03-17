<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_program')->insert([
            [
                'judul' => 'Program Talkshow Inspirasi',
                'video' => '/videos/talkshow-inspirasi.mp4',
                'thumbnail' => '/uploads/programs/talkshow-thumbnail.jpg',
                'deskripsi' => 'Program talkshow yang menghadirkan narasumber inspiratif dari berbagai bidang.',
                'deskripsi_pendek' => 'Talkshow bersama narasumber inspiratif.',
                'id_acara' => 1, // Pastikan id_acara=1 ada di tabel tb_acara
                'tanggal' => '2025-03-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Program Liputan Khusus',
                'video' => '/videos/liputan-khusus.mp4',
                'thumbnail' => '/uploads/programs/liputan-thumbnail.jpg',
                'deskripsi' => 'Liputan khusus tentang isu-isu hangat dan mendalam.',
                'deskripsi_pendek' => 'Liputan mendalam tentang isu terkini.',
                'id_acara' => 2, // id_acara=2 juga harus tersedia di tb_acara
                'tanggal' => '2025-03-25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
