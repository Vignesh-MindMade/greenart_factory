<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\PageSectionSeeder;
use Database\Seeders\CoreValueSeeder;
use Database\Seeders\AboutStorySeeder;
use Database\Seeders\TeamMemberSeeder;
use Database\Seeders\JobPostingSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(PageSectionSeeder::class);
        $this->call(WhyChooseUsSeeder::class);
        $this->call(CoreValueSeeder::class);
        $this->call(AboutStorySeeder::class);
        $this->call(TeamMemberSeeder::class);
        $this->call(JobPostingSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
