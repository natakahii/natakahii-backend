<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    /**
     * Resolve a product variant from selected attribute values.
     *
     * Expects query param: ?attribute_value_ids=1,4,7
     */
    public function resolve(Product $product, Request $request): JsonResponse
    {
        $request->validate([
            'attribute_value_ids' => ['required', 'string'],
        ]);

        $ids = collect(explode(',', $request->attribute_value_ids))
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values();

        $signature = $ids->implode('-');

        $variant = ProductVariant::query()
            ->where('product_id', $product->id)
            ->where('variant_signature', $signature)
            ->with(['attributeValues.attribute', 'attributeValues.attributeValue'])
            ->first();

        if (! $variant) {
            return response()->json([
                'message' => 'No matching variant found for the selected attributes.',
            ], 404);
        }

        return response()->json([
            'variant' => new ProductVariantResource($variant),
        ]);
    }
}
