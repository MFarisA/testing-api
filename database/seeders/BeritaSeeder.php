<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ID yang dirujuk tersedia di database
        $idUploader = DB::table('users')->value('id') ?? 1;
        $idKategori = DB::table('tb_kategori')->value('id') ?? 1;
        $programId = DB::table('tb_program')->value('id_program') ?? null; // Bisa bernilai null jika tidak ada data

        DB::table('tb_berita')->insertOrIgnore([
            'judul' => 'Judul Berita Pertama',
            'path_media' => '/uploads/berita/berita-pertama.jpg',
            'link' => 'https://example.com/berita-pertama',
            'filename' => 'berita-pertama.jpg',
            'deskripsi' => 'Ini adalah deskripsi berita pertama.',
            'waktu' => now(),
            'id_uploader' => $idUploader, // Pastikan user ada
            'id_kategori' => $idKategori, // Pastikan kategori ada
            'publish' => 1,
            'open' => 0,
            'cover' => '/uploads/cover/berita-pertama.jpg',
            'keyword' => 'berita, pertama, headline',
            'editor' => 1,
            'library' => 0,
            'redaktur' => 1,
            'waktu_publish' => Carbon::now(),
            'program_id' => $programId, // Bisa null jika tidak ada program
            'type' => 'video',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
