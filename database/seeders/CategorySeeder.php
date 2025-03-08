<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create 5 parent categories
         Category::factory()->count(5)->create();

         // Create 10 child categories with random parent categories
         Category::factory()->count(10)->create([
             'parent_category_id' => Category::inRandomOrder()->first()->id,
         ]);
    }
}
