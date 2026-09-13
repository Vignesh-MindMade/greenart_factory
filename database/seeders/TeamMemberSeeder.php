<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name'      => 'John Smith',
                'job_title' => 'Company CEO',
                'sort_order' => 1,
                'image'     => 'john-smith.png',
            ],
            [
                'name'      => 'David Johnson',
                'job_title' => 'Co-Founder',
                'sort_order' => 2,
                'image'     => 'david-johnson.png',
            ],
            [
                'name'      => 'Mary Johnson',
                'job_title' => 'Property Manager',
                'sort_order' => 3,
                'image'     => 'mary-johnson.png',
            ],
            [
                'name'      => 'Patricia Davis',
                'job_title' => 'Estate Consultant',
                'sort_order' => 4,
                'image'     => 'patricia-davis.png',
            ],
            [
                'name'      => 'Patricia Davis',
                'job_title' => 'Estate Consultant',
                'sort_order' => 5,
                'image'     => 'mary-johnson.png',
            ],
        ];

        $assetsPath = __DIR__ . '/assets/team-members/';

        foreach ($members as $member) {
            $imageFile = $assetsPath . $member['image'];
            unset($member['image']);

            $record = TeamMember::updateOrCreate(
                ['name' => $member['name'], 'sort_order' => $member['sort_order']],
                $member + ['status' => 'published']
            );

            if (! $record->getFirstMedia('team_member_photos') && file_exists($imageFile)) {
                try {
                    $record->addMedia($imageFile)
                        ->preservingOriginal()
                        ->toMediaCollection('team_member_photos');
                } catch (\Throwable $e) {
                    // Skip silently if the image can't be attached (e.g. offline dev environment)
                }
            }
        }
    }
}
