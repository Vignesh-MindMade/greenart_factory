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

            'about_hero'         => [
                'title'        => 'ABOUT US',
                'stat_1_value' => '250+',
                'stat_1_label' => 'Projects Completed',
                'stat_2_value' => '15+',
                'stat_2_label' => 'Years of Experience',
                'stat_3_value' => '3+',
                'stat_3_label' => 'Countries Served',
                'stat_4_value' => '99%',
                'stat_4_label' => 'Client Satisfaction',
            ],
'about_story'        => ['title' => 'The Road Here', 'subtitle' => null],
'about_mission'      => [
    'title'       => 'Our Mission',
    'description' => "Our mission is to empower businesses with innovative digital solutions that drive sustainable growth and long-term success. We combine creativity, technology, and strategic thinking to build impactful products and experiences. By understanding every client's unique goals, we deliver solutions that are reliable, scalable, and future-ready.",
],
'about_vision'       => [
    'title'       => 'Our Vision',
    'description' => 'Our vision is to become a trusted global leader in digital innovation, transforming the way businesses grow and connect in a technology-driven world. We envision a future where creativity and technology work seamlessly together to solve complex challenges.',
],
'about_core_values'  => [
    'title'       => 'CORE VALUES',
    'description' => 'Our core values shape every project we create and every relationship we build. We are committed to quality, sustainability, innovation, and exceptional craftsmanship.',
],
'about_team'         => ['title' => 'Meet Our Team', 'subtitle' => 'Our dedicated team of biophilic design experts...'],
'about_facility'     => [
    'title'       => 'Workshop and Production Facility',
    'subtitle'    => 'Workshop & Production Facility',
    'description' => 'A specialized interior landscape and biophilic design firm, crafting greener, healthier spaces with meticulous attention to every detail.',
    'cta_label'  => 'Contact Us',
    'cta_url'    => '/contact-us',
    'cta2_label' => 'Book a Studio Visit',
    'cta2_url'   => '/contact-us#studio-visit',
],
'about_certifications' => [
    'title'       => 'Certifications',
    'subtitle'    => 'Certified Excellence. Sustainable Commitment.',
    'description' => 'Our certifications reflect our unwavering commitment to the highest standards of quality, sustainability, and safety in every project we undertake.',
],
'about_clients' => [
    'title'       => 'Our Clients',
    'description' => 'Trusted by leading businesses and organizations for premium botanical solutions tailored to every space. Our long-term partnerships reflect our commitment to quality, reliability, and exceptional service.',
],
        ];

        foreach ($sections as $key => $attributes) {
            PageSection::updateOrCreate(['section_key' => $key], $attributes);
        }
    }
}