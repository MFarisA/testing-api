<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // UserSeeder::class,
            // SuperAdminSeeder::class,
            // KategoriSeeder::class,
            // BeritaSeeder::class,
            // MarketingSeeder::class,
            // AcaraSeeder::class,
            // ProgramSeeder::class,
            // OurexpertiseSeeder::class,
            // Ourexpertise2Seeder::class,
            // HomeSliderSeeder::class,
            // HomeWhoWeAreSeeder::class,
            // OurProgramsSeeder::class,
            // RecentTrailerSeeder::class,
            // SptdinusSliderSeeder::class,
            // SptdinusSlidesTitleSeeder::class,
            // SptudinusSidebarBannerSeeder::class,
            NotificationCategorySeeder::class,
            // PermissionSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
