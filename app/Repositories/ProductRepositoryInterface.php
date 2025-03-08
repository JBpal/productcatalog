<?php

namespace App\Repositories;

use App\BO\ProductBO;

interface ProductRepositoryInterface
{
    public function getAll($categoryId = null, $searchKeyword = null);
    public function getById($id);
    public function create(ProductBO $productBO);
    public function update($id, ProductBO $productBO);
    public function delete($id);
}