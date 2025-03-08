<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repositories\CategoryRepository;
use App\Models\Category;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $categoryRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = new CategoryRepository();
    }

    // Test for getAll() method
    public function test_get_all_categories()
    {
        // Create a top-level category
        $parentCategory = Category::factory()->create([
            'name' => 'Parent Category',
            'parent_category_id' => null,
        ]);

        // Create a child category for the parent
        $childCategory = Category::factory()->create([
            'name' => 'Child Category',
            'parent_category_id' => $parentCategory->id,
        ]);

        // Call the getAll method
        $categories = $this->categoryRepository->getAll();

        // Assert that the response is a collection
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $categories);

        // Assert that there is one top-level category
        $this->assertCount(1, $categories);

        // Assert that the top-level category is the one we created
        $this->assertEquals('Parent Category', $categories->first()->name);

        // Assert that the top-level category has one child
        $this->assertCount(1, $categories->first()->children);

        // Assert that the child category is the one we created
        $this->assertEquals('Child Category', $categories->first()->children->first()->name);
    }
}
