<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $activities = Activity::query()
            ->published()
            ->with(['images.media'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->cursorPaginate(10);

        return ActivityResource::collection($activities);
    }
}
