<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\StoreVendorProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use App\Support\CdnHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorProductController extends Controller
{
    /**
     * Create a new product for the authenticated vendor.
     */
    public function store(StoreVendorProductRequest $request): JsonResponse
    {
        $user = $request->user('api');
        $vendor = $user->vendor;

        if (! $vendor || ! $vendor->isApproved()) {
            return response()->json([
                'message' => 'Your vendor account must be approved before listing products.',
            ], 403);
        }

        $product = DB::transaction(function () use ($request, $vendor) {
            $product = Product::create([
                'vendor_id' => $vendor->id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name).'-'.Str::random(6),
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'stock' => $request->stock,
                'status' => $request->input('status', 'draft'),
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $cdnUrl = CdnHelper::upload($image, 'products/'.$product->id);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $cdnUrl,
                    ]);
                }
            }

            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $attributeIds = collect($variantData['attributes'])
                        ->sortBy('attribute_id')
                        ->pluck('attribute_value_id')
                        ->implode('-');

                    $variant = ProductVariant::create([
                        'vendor_id' => $vendor->id,
                        'product_id' => $product->id,
                        'variant_signature' => $attributeIds,
                        'sku' => $variantData['sku'],
                        'price' => $variantData['price'],
                        'discount_price' => $variantData['discount_price'] ?? null,
                        'stock' => $variantData['stock'],
                        'status' => 'active',
                    ]);

                    foreach ($variantData['attributes'] as $attr) {
                        VariantAttributeValue::create([
                            'variant_id' => $variant->id,
                            'attribute_id' => $attr['attribute_id'],
                            'attribute_value_id' => $attr['attribute_value_id'],
                        ]);
                    }
                }
            }

            return $product;
        });

        return response()->json([
            'message' => 'Product created successfully.',
            'product' => new ProductResource($product->load(['images', 'variants.attributeValues', 'category'])),
        ], 201);
    }
}
