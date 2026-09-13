<?php

namespace Database\Seeders;

use App\Models\CoreValue;
use Illuminate\Database\Seeder;

class CoreValueSeeder extends Seeder
{
    public function run(): void
    {
        $values = [
            [
                'title'       => 'Quality First',
                'description' => 'We deliver exceptional craftsmanship and premium greenery with meticulous attention to every detail.',
                'sort_order'  => 1,
            ],
            [
                'title'       => 'Sustainability',
                'description' => 'We embrace eco-friendly practices and create greener spaces that support a healthier environment.',
                'sort_order'  => 2,
            ],
            [
                'title'       => 'Customer Commitment',
                'description' => 'We build lasting relationships through reliable service, transparent communication, and personalized solutions.',
                'sort_order'  => 3,
            ],
        ];

        foreach ($values as $value) {
            CoreValue::updateOrCreate(
                ['title' => $value['title']],
                $value + ['status' => 'published']
            );
        }
    }
}
