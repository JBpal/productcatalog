# Product Catalog API

This is a RESTful API for managing a product catalog. It allows you to perform CRUD operations on products and categories, with support for filtering, searching, and caching.

## Table of Contents

1. [Features](#features)
2. [Technologies Used](#technologies-used)
3. [Setup Instructions](#setup-instructions)
4. [API Endpoints](#api-endpoints)
5. [Error Handling](#error-handling)
6. [Testing](#testing)
7. [Caching](#caching)

---

## Features

- **Product Management**:
  - Retrieve a paginated list of products.
  - Filter products by category.
  - Search products by name or description.
  - Create, update, and delete products.

- **Category Management**:
  - Retrieve a list of categories with parent-child relationships.
  - Nested structure for categories.

- **Caching**:
  - Cache frequently accessed data (e.g., product lists, category lists).

- **API Versioning**:
  - Support for versioned API endpoints (e.g., `/api/v1/products`).

---

## Technologies Used

- **Backend**: Laravel 11
- **Database**: MySQL
- **Caching**: Laravel Cache (File)
- **Testing**: PHPUnit
- **API Documentation**: Swagger

---

## Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- MySQL
- Redis (optional, for caching)

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/product-catalog-api.git
   cd product-catalog-api

2. Install dependencies:
    composer install

3. Update the following variables in .env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=product_catalog
    DB_USERNAME=root
    DB_PASSWORD=
    CACHE_DRIVER=file

4. Run migrations and seed the database
    php artisan migrate --seed

5. Start the development server
    php artisan serve
6. Access the API at http://localhost:8000/api
7. API Documentation
    http://localhost:8000/api/documentation

## API Endpoints

1. Products
    - GET /api/v1/products: Retrieve a paginated list of products (10 per page).

    - Query Parameters:
    - category_id (optional): Filter products by category ID.
    - search (optional): Search products by name or description.

    - GET /api/v1/products/{id}: Retrieve a specific product by ID.

    - POST /api/v1/products: Create a new product
    - PUT /api/v1/products/{id}: Update an existing product.
    - DELETE /api/v1/products/{id}: Delete a product.

2. Categories
     - GET /api/v1/categories: Retrieve a list of all categories, including parent-child relationships.

## Testing
    - php artisan test

## Caching
    - Frequently accessed data (e.g., product lists, category lists) is cached using Laravel's caching mechanisms. Cache is cleared automatically when products or categories are updated or deleted.