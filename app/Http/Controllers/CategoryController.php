<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Cache;


class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {

        $cacheKey = 'categories_all';
        $categories = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return $this->categoryRepository->getAll();
        });
        return response()->json($categories);
    }

    public function clearCache()
    {
        Cache::flush();
        return response()->json(['message' => 'Category cache cleared successfully']);
    }
}
