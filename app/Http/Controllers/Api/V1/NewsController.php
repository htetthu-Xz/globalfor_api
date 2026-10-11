<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsItemResource;
use App\Models\NewsItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $request->boolean('homepage')
            ? NewsItem::query()->visibleOnHomepage()
            : NewsItem::query()->published()->orderByDesc('created_at')->orderByDesc('id');

        return NewsItemResource::collection(
            $query->with('coverMedia')->paginate(10),
        );
    }

    public function show(string $slug): NewsItemResource
    {
        $newsItem = NewsItem::query()
            ->published()
            ->with('coverMedia')
            ->where('slug', $slug)
            ->firstOrFail();

        return NewsItemResource::make($newsItem);
    }
}
