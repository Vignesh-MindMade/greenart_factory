<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use Illuminate\Database\Seeder;

class JobPostingSeeder extends Seeder
{
    public function run(): void
    {
        $postings = [
            [
                'title' => 'Landscape Design Engineer',
                'slug' => 'landscape-design-engineer',
                'department' => 'Design',
                'location' => 'Dubai, UAE',
                'employment_type' => 'full_time',
                'description' => '<p>Design and plan landscape and green wall installations for residential and commercial clients across the UAE.</p>',
                'status' => 'published',
            ],
            [
                'title' => 'Horticulture Specialist',
                'slug' => 'horticulture-specialist',
                'department' => 'Operations',
                'location' => 'Dubai, UAE',
                'employment_type' => 'full_time',
                'description' => '<p>Maintain plant health across installed projects and advise on species selection for new proposals.</p>',
                'status' => 'published',
            ],
            [
                'title' => 'Sales Coordinator (Internship)',
                'slug' => 'sales-coordinator-internship',
                'department' => 'Sales',
                'location' => 'Dubai, UAE',
                'employment_type' => 'internship',
                'description' => '<p>Support the sales team with client follow-ups, proposal preparation, and CRM data entry.</p>',
                'status' => 'draft',
            ],
        ];

        foreach ($postings as $posting) {
            JobPosting::updateOrCreate(
                ['slug' => $posting['slug']],
                $posting
            );
        }
    }
}
