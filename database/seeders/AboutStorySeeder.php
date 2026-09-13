<?php

namespace Database\Seeders;

use App\Models\AboutStory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AboutStorySeeder extends Seeder
{
    public function run(): void
    {
        $stories = [
            [
                'tab_label' => 'Overview',
                'title'     => 'The Full Picture',
                'content'   => "Green Art Factory brings together design, craftsmanship, and sustainability under one roof, delivering green installations that are as functional as they are beautiful.\n\nFrom first sketch to final install, every project is treated as a long-term investment in the space it lives in.",
                'image'     => 'https://green-art-factory.vercel.app/images/about-stories-1.jpg',
                'sort_order' => 1,
            ],
            [
                'tab_label' => 'Introduction',
                'title'     => 'Who We Are',
                'content'   => "We're a team of designers, horticulturists, and builders who believe interiors should feel alive — not decorated at, but genuinely inhabited by nature.\n\nEvery installation starts with the space itself: its light, its use, and the people who move through it every day.",
                'image'     => 'https://green-art-factory.vercel.app/images/about-stories-2.jpg',
                'sort_order' => 2,
            ],
            [
                'tab_label' => 'Origins',
                'title'     => 'Where It All Began',
                'content'   => "Green Art Factory was founded with a simple belief: every space deserves the calming presence of nature. What began as a passion for botanical design has grown into a brand dedicated to crafting premium green installations that combine artistry, innovation, and lasting quality. From handcrafted moss creations to bespoke landscapes, our journey is rooted in transforming interiors into timeless, inspiring environments.\n\nOur journey began with a passion for bringing nature closer to everyday living. Guided by creativity and craftsmanship, we set out to design botanical installations that inspire, endure, and elevate every environment. Every project reflects our dedication to quality, innovation, and attention to detail. Today, we continue to create timeless green spaces that leave a lasting impression.",
                'image'     => 'https://green-art-factory.vercel.app/images/about-stories-3.jpg',
                'sort_order' => 3,
            ],
            [
                'tab_label' => 'Purpose',
                'title'     => 'Why We Do This',
                'content'   => "Wellbeing isn't a mood board word for us — it's measurable. Greenery lowers stress, improves air quality, and makes people want to spend more time in the spaces they occupy.\n\nEvery installation is built around that outcome first, aesthetics second — though we've found the two are rarely in conflict.",
                'image'     => 'https://green-art-factory.vercel.app/images/about-stories-4.jpg',
                'sort_order' => 4,
            ],
            [
                'tab_label' => 'Services',
                'title'     => 'What We Build',
                'content'   => "Preserved moss walls, living green walls, vertical gardens, bespoke artificial trees, and full biophilic design and build — each service engineered for the specific climate and light conditions of its space.\n\nWe handle everything from initial concept through installation and ongoing care, so the result stays as striking on day 1000 as it was on day one.",
                'image'     => 'https://green-art-factory.vercel.app/images/about-stories-5.jpg',
                'sort_order' => 5,
            ],
            [
                'tab_label' => 'Story',
                'title'     => 'The Road Here',
                'content'   => "From a single moss wall commission to a full-scale interior landscaping studio — every project along the way taught us something about what makes a green space actually work, not just look good in a photo.\n\nThat accumulated craft is what goes into every installation we deliver today.",
                'image'     => 'https://green-art-factory.vercel.app/images/about-stories-6.jpg',
                'sort_order' => 6,
            ],
        ];

        foreach ($stories as $story) {
            $imageUrl = $story['image'];
            unset($story['image']);

            $slug = Str::slug($story['title']);

            $record = AboutStory::updateOrCreate(
                ['slug' => $slug],
                $story + ['slug' => $slug, 'status' => 'published']
            );

            if (! $record->getFirstMedia('about_story_images')) {
                try {
                    $record->addMediaFromUrl($imageUrl)->toMediaCollection('about_story_images');
                } catch (\Throwable $e) {
                    // Skip silently if the remote image can't be fetched (e.g. offline dev environment)
                }
            }
        }
    }
}
