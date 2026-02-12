<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Video feed - paginated list of video media for video commerce.
     */
    public function feed(Request $request): JsonResponse
    {
        $videos = Media::query()
            ->where('type', 'video')
            ->with(['product.vendor', 'product.images'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'videos' => MediaResource::collection($videos),
            'meta' => [
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total(),
            ],
        ]);
    }
}
