<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutStory;
use App\Models\Certification;
use App\Models\CertificationBadge;
use App\Models\CoreValue;
use App\Models\PageSection;
use App\Models\Partners;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;

class AboutController extends Controller
{
    // GET /api/about
    // Powers the whole About Us page in a single call: sections, story tabs,
    // team members, certifications, certification badges, core values and clients.
    public function index(): JsonResponse
    {
        $sections = PageSection::query()
            ->where('section_key', 'like', 'about_%')
            ->with('media')
            ->get()
            ->keyBy('section_key')
            ->map(fn ($s) => [
                'title'       => $s->title,
                'subtitle'    => $s->subtitle,
                'description' => $s->description,
                'cta_label'   => $s->cta_label,
                'cta_url'     => $s->cta_url,
                'cta2_label'  => $s->cta2_label,
                'cta2_url'    => $s->cta2_url,
                'image'       => $s->getFirstMediaUrl('page_section_image') ?: null,
                'gallery'     => $s->getMedia('page_section_gallery')->map(fn ($m) => $m->getUrl())->values(),
                'video'       => $s->getFirstMediaUrl('page_section_video') ?: null,
                'stats'       => [
                    ['value' => $s->stat_1_value, 'label' => $s->stat_1_label],
                    ['value' => $s->stat_2_value, 'label' => $s->stat_2_label],
                    ['value' => $s->stat_3_value, 'label' => $s->stat_3_label],
                    ['value' => $s->stat_4_value, 'label' => $s->stat_4_label],
                ],
            ]);

        $stories = AboutStory::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->with('media')
            ->get()
            ->map(fn ($story) => [
                'id'        => $story->id,
                'tab_label' => $story->tab_label,
                'title'     => $story->title,
                'slug'      => $story->slug,
                'content'   => $story->content,
                'image'     => $story->getFirstMediaUrl('about_story_images'),
            ]);

        $team = TeamMember::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->with('media')
            ->get()
            ->map(fn ($member) => [
                'id'        => $member->id,
                'name'      => $member->name,
                'job_title' => $member->job_title,
                'company'   => $member->company,
                'photo'     => $member->getFirstMediaUrl('team_member_photos'),
            ]);

        $certifications = Certification::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->with('media')
            ->get()
            ->map(fn ($certification) => [
                'id'             => $certification->id,
                'standard_code'  => $certification->standard_code,
                'title'          => $certification->title,
                'description'    => $certification->description,
                'logo'           => $certification->getFirstMediaUrl('certification_logos') ?: null,
            ]);

        $badges = CertificationBadge::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->with('media')
            ->get()
            ->map(fn ($badge) => [
                'id'       => $badge->id,
                'title'    => $badge->title,
                'subtitle' => $badge->subtitle,
                'image'    => $badge->getFirstMediaUrl('certification_badge_images'),
            ]);

        $coreValues = CoreValue::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($value) => [
                'id'          => $value->id,
                'title'       => $value->title,
                'description' => $value->description,
            ]);

        $partners = Partners::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->with('media')
            ->get()
            ->map(fn ($p) => [
                'id'   => $p->id,
                'name' => $p->name,
                'logo' => $p->getFirstMediaUrl('partners'),
            ]);

        return response()->json([
            'data' => [
                'sections'       => $sections,
                'stories'        => $stories,
                'team'           => $team,
                'certifications' => $certifications,
                'badges'         => $badges,
                'core_values'    => $coreValues,
                'partners'       => $partners,
            ],
        ]);
    }
}
