<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Ourexpertise2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('v2_home_ourexpertise2')->insert([
            [
                'thumbnail' => '/uploads/expertise2/branding.jpg',
                'judul' => 'Branding Strategy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thumbnail' => '/uploads/expertise2/uiux.jpg',
                'judul' => 'UI/UX Design',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thumbnail' => '/uploads/expertise2/webdev.jpg',
                'judul' => 'Web Development',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
