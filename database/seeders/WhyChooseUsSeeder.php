<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

/**
 * "Why Choose Us" on the products page. Copy taken from the Figma design
 * (products screen, node 180:287).
 */
class WhyChooseUsSeeder extends Seeder
{
    public function run(): void
    {
        $section = PageSection::firstOrCreate(
            ['section_key' => 'products'],
            ['title' => 'OUR PRODUCTS'],
        );

        $items = [
            ['01', 'Exceptional Craftsmanship', 'Every installation is meticulously designed and handcrafted to achieve a natural, elegant aesthetic that enhances every space.'],
            ['02', 'Premium Quality Materials', 'We use carefully selected, long-lasting materials that deliver realistic beauty with minimal maintenance and enduring performance.'],
            ['03', 'Tailored Design Solutions', 'From bespoke installations to large-scale commercial projects, every solution is customized to complement your vision and environment.'],
            ['04', 'Sustainable Approach', 'Our designs bring the beauty of nature indoors while offering eco-conscious, low-maintenance alternatives for modern spaces.'],
            ['05', 'End-to-End Expertise', 'From consultation and design to installation and aftercare, our experienced team ensures a seamless experience at every stage.'],
        ];

        foreach ($items as $index => [$displayNo, $title, $description]) {
            ContentBlock::updateOrCreate(
                [
                    'blockable_type' => $section->getMorphClass(),
                    'blockable_id'   => $section->id,
                    'group'          => ContentBlock::GROUP_WHY_CHOOSE_US,
                    'title'          => $title,
                ],
                [
                    'display_no'  => $displayNo,
                    'description' => $description,
                    'sort_order'  => $index + 1,
                    'status'      => 'published',
                ],
            );
        }
    }
}
