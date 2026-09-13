<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\JobPostingResource;
use App\Mail\JobApplicationConfirmationMail;
use App\Mail\JobApplicationReceivedMail;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CareersController extends Controller
{
    /** Hard ceiling so ?per_page=1000000 cannot be used to dump the listing. */
    private const MAX_PER_PAGE = 48;

    /**
     * GET /api/v1/careers — published job openings, with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->integer('per_page', 15), self::MAX_PER_PAGE);

        $query = JobPosting::query()->where('status', 'published');

        if ($request->filled('department')) {
            $query->where('department', $request->string('department'));
        }

        if ($request->filled('location')) {
            $query->where('location', $request->string('location'));
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->string('employment_type'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->string('search') . '%');
        }

        $postings = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'data' => JobPostingResource::collection($postings->getCollection()),
            'meta' => [
                'current_page' => $postings->currentPage(),
                'last_page' => $postings->lastPage(),
                'per_page' => $postings->perPage(),
                'total' => $postings->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/careers/{slug} — a single published job opening.
     */
    public function show(string $slug): JsonResponse
    {
        $posting = JobPosting::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return response()->json([
            'data' => new JobPostingResource($posting),
        ]);
    }

    /**
     * POST /api/v1/careers/{slug}/apply — submit an application with a CV.
     */
    public function apply(StoreJobApplicationRequest $request, string $slug): JsonResponse
    {
        $posting = JobPosting::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $application = JobApplication::create([
            'job_posting_id' => $posting->id,
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'cover_note' => $request->validated('cover_note'),
            'status' => 'new',
        ]);

        $application->addMediaFromRequest('resume')->toMediaCollection('resume');

        $hrEmail = config('mail.hr_notification_address');

        if ($hrEmail) {
            Mail::to($hrEmail)->send(new JobApplicationReceivedMail($application));
        }

        Mail::to($application->email)->send(new JobApplicationConfirmationMail($application));

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'data' => [
                'id' => $application->id,
            ],
        ], 201);
    }
}
