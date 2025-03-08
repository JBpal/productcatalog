<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\BO\ProductBO;

/**
 * @OA\Info(
 *     title="Product API",
 *     version="1.0.0",
 *     description="API for managing products"
 * )
 *
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Product Name"),
 *     @OA\Property(property="description", type="string", example="Product Description"),
 *     @OA\Property(property="sku", type="string", example="PROD123"),
 *     @OA\Property(property="price", type="number", format="float", example=99.99),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="ProductRequest",
 *     type="object",
 *     required={"name", "sku", "price", "category_id"},
 *     @OA\Property(property="name", type="string", example="Product Name"),
 *     @OA\Property(property="description", type="string", example="Product Description"),
 *     @OA\Property(property="sku", type="string", example="PROD123"),
 *     @OA\Property(property="price", type="number", format="float", example=99.99),
 *     @OA\Property(property="category_id", type="integer", example=1)
 * )
 */
class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter products by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search products by name or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');
        $searchKeyword = $request->query('search');

        $cacheKey = 'products_' . $categoryId . '_' . $searchKeyword;
        $products = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($categoryId, $searchKeyword) {
            Log::info('Caching products for key: products_' . $categoryId . '_' . $searchKeyword);
            return $this->productRepository->getAll($categoryId, $searchKeyword);
        });

        return response()->json($products);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a product by ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product found",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $cacheKey = 'product_' . $id;
            $product = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
                Log::info('Caching products for key: productid_' . $id);
                return $this->productRepository->getById($id);
            });

            return response()->json($product);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                'sku' => 'required|string|unique:products',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
            ]);

            $productBO = new ProductBO();
            $productBO->name = $data['name'];
            $productBO->description = $data['description'];
            $productBO->sku = $data['sku'];
            $productBO->price = $data['price'];
            $productBO->category_id = $data['category_id'];

            $product = $this->productRepository->create($productBO);

            Cache::flush();

            return response()->json([
                'message' => 'Product created successfully.',
                'data' => $product,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string',
                'description' => 'nullable|string',
                'sku' => 'sometimes|string|unique:products,sku,' . $id,
                'price' => 'sometimes|numeric',
                'category_id' => 'sometimes|exists:categories,id',
            ]);

            $productBO = new ProductBO();
            $productBO->name = $data['name'] ?? null;
            $productBO->description = $data['description'] ?? null;
            $productBO->sku = $data['sku'] ?? null;
            $productBO->price = $data['price'] ?? null;
            $productBO->category_id = $data['category_id'] ?? null;

            $product = $this->productRepository->update($id, $productBO);

            Cache::forget('product_' . $id);

            return response()->json([
                'message' => 'Product updated successfully.',
                'data' => $product,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->productRepository->delete($id);

            Cache::forget('product_' . $id);

            return response()->json([
                'message' => 'Product deleted successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }
    }
}