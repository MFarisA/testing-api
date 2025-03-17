<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idUploader = DB::table('users')->exists() ? DB::table('users')->value('id') : null;
        $idKategori = DB::table('tb_kategori')->exists() ? DB::table('tb_kategori')->value('id_kategori') : null;
        $programId = DB::table('tb_program')->exists() ? DB::table('tb_program')->value('id_program') : null;

        if (!$idUploader || !$idKategori) {
            $this->command->error('Seeder gagal: Data users atau tb_kategori tidak ditemukan.');
            return;
        }

        DB::table('tb_berita')->insertOrIgnore([
            'judul' => 'Judul Berita Pertama',
            'path_media' => '/uploads/berita/berita-pertama.jpg',
            'link' => 'https://example.com/berita-pertama',
            'filename' => 'berita-pertama.jpg',
            'deskripsi' => 'Ini adalah deskripsi berita pertama.',
            'waktu' => now(),
            'id_uploader' => $idUploader,
            'id_kategori' => $idKategori,
            'publish' => 1,
            'open' => 0,
            'cover' => '/uploads/cover/berita-pertama.jpg',
            'keyword' => 'berita, pertama, headline',
            'editor' => 1,
            'library' => 0,
            'redaktur' => 1,
            'waktu_publish' => Carbon::now(),
            'program_id' => $programId,
            'type' => 'video',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('BeritaSeeder berhasil dijalankan.');
    }
}
