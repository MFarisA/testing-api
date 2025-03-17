<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class BeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_berita')->insert([
            'judul' => 'Judul Berita Pertama',
            'path_media' => '/uploads/berita/berita-pertama.jpg',
            'link' => 'https://example.com/berita-pertama',
            'filename' => 'berita-pertama.jpg',
            'deskripsi' => 'Ini adalah deskripsi berita pertama.',
            'waktu' => now(),
            'id_uploader' => 1, // pastikan user id 1 ada di tabel users
            'id_kategori' => 1, // pastikan kategori id 1 ada di tabel tb_kategori
            'publish' => 1,
            'open' => 0,
            'cover' => '/uploads/cover/berita-pertama.jpg',
            'keyword' => 'berita, pertama, headline',
            'editor' => 1,
            'library' => 0,
            'redaktur' => 1,
            'waktu_publish' => now(),
            'program_id' => null, // jika ada program isi dengan id program
            'type' => 'video',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
