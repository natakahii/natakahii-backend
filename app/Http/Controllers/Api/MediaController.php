<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Models\Product;
use App\Support\CdnHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * List media for a product.
     */
    public function index(Product $product): JsonResponse
    {
        $media = Media::where('product_id', $product->id)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'media' => MediaResource::collection($media),
        ]);
    }

    /**
     * Upload media for a vendor's product.
     */
    public function store(Product $product, Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:51200'],
            'type' => ['required', 'in:image,video'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $vendor = $request->user('api')->vendor;

        if (! $vendor || $product->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'You do not own this product.'], 403);
        }

        $file = $request->file('file');
        $cdnUrl = CdnHelper::upload($file, "media/{$product->id}");

        $media = Media::create([
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'type' => $request->type,
            'file_path' => $cdnUrl,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Media uploaded.',
            'media' => new MediaResource($media),
        ], 201);
    }

    /**
     * Update media metadata.
     */
    public function update(Media $media, Request $request): JsonResponse
    {
        $vendor = $request->user('api')->vendor;

        if (! $vendor || $media->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $media->update($request->only(['title', 'description', 'sort_order', 'is_featured']));

        return response()->json([
            'message' => 'Media updated.',
            'media' => new MediaResource($media),
        ]);
    }

    /**
     * Delete a media file.
     */
    public function destroy(Media $media, Request $request): JsonResponse
    {
        $vendor = $request->user('api')->vendor;

        if (! $vendor || $media->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        CdnHelper::delete($media->file_path);
        $media->delete();

        return response()->json([
            'message' => 'Media deleted.',
        ]);
    }
}
