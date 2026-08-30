<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'hero'         => ['title' => null, 'subtitle' => null],
            'products'     => ['title' => 'OUR PRODUCTS', 'subtitle' => null],
            'featured'     => ['title' => 'FEATURED PROJECTS', 'subtitle' => null],
            'services'     => ['title' => 'SERVICES', 'subtitle' => null],
            'testimonials' => ['title' => 'TESTIMONIALS', 'subtitle' => 'What Our Customers Say'],
            'partners'     => ['title' => "Clients We've Partnered With", 'subtitle' => null],
            // Shared by every collection gallery page — one design, one copy set.
            'gallery'      => ['title' => 'gallery', 'subtitle' => 'view our gallery'],
            'blog'         => [
                'title'       => 'Blogs & Insights',
                'description' => 'Explore curated articles...',
                'cta_label'   => 'Explore the Blog',
                'cta_url'     => '/blog',
            ],
        ];

        foreach ($sections as $key => $attributes) {
            PageSection::updateOrCreate(['section_key' => $key], $attributes);
        }
    }
}