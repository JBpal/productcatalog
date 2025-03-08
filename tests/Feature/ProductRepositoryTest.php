<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Repositories\ProductRepository;
use App\Models\Product;
use App\Models\Category;
use App\BO\ProductBO;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $productRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->productRepository = new ProductRepository();
    }

    // Test for getAll() method
    public function test_get_all_products()
    {
        // Create 15 products for pagination testing
        Product::factory()->count(15)->create();

        // Test without filters
        $products = $this->productRepository->getAll();
        $this->assertCount(10, $products); // Default pagination is 10 per page
        $this->assertEquals(15, $products->total()); // Total products should be 15

        // Test with category filter
        $category = Category::factory()->create();
        Product::factory()->count(5)->create(['category_id' => $category->id]);

        $filteredProducts = $this->productRepository->getAll($category->id);
        $this->assertCount(5, $filteredProducts); // Only 5 products belong to this category
        $this->assertEquals(5, $filteredProducts->total());

        // Test with search keyword
        $searchProducts = $this->productRepository->getAll(null, 'Test');
        $this->assertTrue($searchProducts->total() >= 0); // At least 0 products match the search
    }

    // Test for getById() method
    public function test_get_product_by_id()
    {
        $product = Product::factory()->create();

        $foundProduct = $this->productRepository->getById($product->id);
        $this->assertInstanceOf(Product::class, $foundProduct);
        $this->assertEquals($product->id, $foundProduct->id);
    }

    // Test for create() method
    public function test_create_product()
    {
        $category = Category::factory()->create();

        $productBO = new ProductBO();
        $productBO->name = 'Test Product';
        $productBO->description = 'This is a test product';
        $productBO->sku = 'TEST123';
        $productBO->price = 100.00;
        $productBO->category_id = $category->id;

        $product = $this->productRepository->create($productBO);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals($category->id, $product->category_id);
    }

    // Test for update() method
    public function test_update_product()
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();

        $productBO = new ProductBO();
        $productBO->name = 'Updated Product';
        $productBO->description = 'This is an updated product';
        $productBO->sku = 'UPDATED123';
        $productBO->price = 200.00;
        $productBO->category_id = $category->id;

        $updatedProduct = $this->productRepository->update($product->id, $productBO);
        $this->assertInstanceOf(Product::class, $updatedProduct);
        $this->assertEquals('Updated Product', $updatedProduct->name);
        $this->assertEquals($category->id, $updatedProduct->category_id);
    }

    // Test for delete() method
    public function test_delete_product()
    {
        $product = Product::factory()->create();

        $this->productRepository->delete($product->id);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}