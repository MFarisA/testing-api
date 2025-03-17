<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OurProgramsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('v2_our_programs')->insert([
            [
                'thumbnail' => '/uploads/programs/program1.jpg',
                'judul' => 'Digital Transformation',
                'deskripsi' => 'Helping businesses digitize their processes for efficiency and growth.',
                'link' => '/programs/digital-transformation',
                'urutan' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thumbnail' => '/uploads/programs/program2.jpg',
                'judul' => 'Sustainable Development',
                'deskripsi' => 'Promoting sustainable and eco-friendly business practices.',
                'link' => '/programs/sustainable-development',
                'urutan' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
