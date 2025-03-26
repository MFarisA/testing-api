<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RolePermissionSeeder::class,
            KategoriSeeder::class,
            BeritaSeeder::class,
            MarketingSeeder::class,
            AcaraSeeder::class,
            ProgramSeeder::class,
            OurexpertiseSeeder::class,
            Ourexpertise2Seeder::class,
            HomeSliderSeeder::class,
            HomeWhoWeAreSeeder::class,
            OurProgramsSeeder::class,
            RecentTrailerSeeder::class,
            SptdinusSliderSeeder::class,
            SptdinusSlidesTitleSeeder::class,
            SptudinusSidebarBannerSeeder::class,
        ]);
    }
}
