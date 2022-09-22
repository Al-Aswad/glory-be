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
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'price' => $this->faker->randomNumber(4),
            'stock' => $this->faker->randomNumber(2),
            'image' => $this->faker->imageUrl(),
            // 'created_at' => $this->faker->dateTime(),
            // 'updated_at' => $this->faker->dateTime(),
        ];
    }
}
