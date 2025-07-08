<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $idUploader = DB::table('users')->exists() ? DB::table('users')->value('id') : null;
        $idKategori = DB::table('tb_kategori')->exists() ? DB::table('tb_kategori')->value('id_kategori') : null;
        $programId = DB::table('tb_program')->exists() ? DB::table('tb_program')->value('id_program') : null;

        if (!$idUploader || !$idKategori) {
            $this->command->error('Seeder gagal: Data users atau tb_kategori tidak ditemukan.');
            return;
        }

        $data = [];

        for ($i = 0; $i < 23; $i++) {
            $judul = $faker->sentence(5);
            $slug = Str::slug($judul);

            $data[] = [
                'judul' => $judul,
                'path_media' => '/uploads/berita/' . $slug . '.jpg',
                'link' => $faker->url,
                'filename' => $slug . '.jpg',
                'deskripsi' => $faker->paragraph,
                'waktu' => now(),
                'id_uploader' => $idUploader,
                'id_kategori' => $idKategori,
                'publish' => $faker->boolean,
                'open' => $faker->boolean,
                'cover' => '/uploads/cover/' . $slug . '.jpg',
                'keyword' => implode(', ', $faker->words(3)),
                'editor' => $faker->boolean,
                'library' => $faker->boolean,
                'redaktur' => $faker->boolean,
                'waktu_publish' => Carbon::now()->addDays(rand(0, 10)),
                'program_id' => $programId,
                'type' => $faker->randomElement(['video', 'audio', 'artikel']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('tb_berita')->insertOrIgnore($data);

        $this->command->info('BeritaSeeder berhasil membuat 23 data menggunakan Faker.');
    }
}
