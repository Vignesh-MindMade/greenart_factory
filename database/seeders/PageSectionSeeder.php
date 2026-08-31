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
            'portfolio'    => [
                'title'       => 'Creating Living Spaces Inspired by Nature, Designed for Modern Living',
                'subtitle'    => 'portfolio',
                'description' => 'Explore curated landscape and botanical installations that transform everyday environments into memorable experiences.',
                'cta_label'   => 'View Products',
                'cta_url'     => '/products',
            ],
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