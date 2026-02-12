<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'vendor_id' => Vendor::factory(),
            'variant_signature' => fake()->unique()->uuid(),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-????')),
            'price' => fake()->randomFloat(2, 1000, 500000),
            'discount_price' => null,
            'stock' => fake()->numberBetween(0, 100),
            'status' => 'active',
        ];
    }
}
