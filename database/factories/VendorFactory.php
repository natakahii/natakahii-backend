<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shopName = fake()->company();

        return [
            'user_id' => User::factory(),
            'shop_name' => $shopName,
            'shop_slug' => Str::slug($shopName).'-'.fake()->unique()->randomNumber(4),
            'description' => fake()->paragraph(),
            'logo' => null,
            'commission_rate' => fake()->randomFloat(2, 5, 20),
            'status' => 'approved',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }
}
