<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

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

    protected $model = Product::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3), // Generate a random product name
            'description' => $this->faker->paragraph, // Generate a random description
            'sku' => $this->faker->unique()->ean13, // Generate a unique SKU
            'price' => $this->faker->randomFloat(2, 10, 1000), // Generate a random price between 10 and 1000
            'category_id' => \App\Models\Category::factory(), // Associate with a category
        ];
    }
}
