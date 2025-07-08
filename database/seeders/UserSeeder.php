<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;



class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
    
        foreach (range(1, 23) as $index) {
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),

                'remember_token' => $faker->uuid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        DB::table('users')->insert([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => $faker->uuid,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
}
