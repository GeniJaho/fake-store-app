<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fake_store_id' => 1,
            'title' => fake()->name,
            'price' => fake()->numberBetween(1, 10000),
            'description' => fake()->sentence,
            'category' => fake()->word,
            'image' => fake()->imageUrl,
            'rating_rate' => fake()->randomFloat(1, 1, 5),
            'rating_count' => fake()->numberBetween(1, 1000),
        ];
    }
}
