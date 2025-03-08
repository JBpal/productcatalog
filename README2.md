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
8. [Rate Limiting](#rate-limiting)
9. [API Documentation](#api-documentation)
10. [Bonus Features](#bonus-features)
11. [Contributing](#contributing)
12. [License](#license)

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

3. Run migrations and seed the database
    php artisan migrate --seed

4. Start the development server
    php artisan serve
5. Access the API at http://localhost:8000/api
6. API Documentation
    http://localhost:8000/api/documentation