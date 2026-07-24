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
            'blog'         => [
                'title'       => 'Blogs & Insights',
                'description' => 'Explore curated articles...',
                'cta_label'   => 'Explore the Blog',
                'cta_url'     => '/blog',
            ],

            'about_hero'         => ['title' => 'ABOUT US'],
'about_story'        => ['title' => 'The Road Here', 'subtitle' => null],
'about_mission'      => ['title' => 'Our Mission'],
'about_vision'       => ['title' => 'Our Vision'],
'about_core_values'  => ['title' => 'CORE VALUES'],
'about_team'         => ['title' => 'Meet Our Team', 'subtitle' => 'Our dedicated team of biophilic design experts...'],
'about_facility'     => [
    'title'      => 'Workshop and Production Facility',
    'subtitle'   => 'Workshop & Production Facility',
    'cta_label'  => 'Contact Us',
    'cta_url'    => '/contact-us',
    'cta2_label' => 'Book a Studio Visit',
    'cta2_url'   => '/contact-us#studio-visit',
],
'about_certifications' => ['title' => 'Certifications', 'subtitle' => 'Certified Excellence. Sustainable Commitment.'],
        ];

        foreach ($sections as $key => $attributes) {
            PageSection::updateOrCreate(['section_key' => $key], $attributes);
        }
    }
}