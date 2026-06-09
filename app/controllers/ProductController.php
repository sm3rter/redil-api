<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductController extends Controller
{
    /**
     * Get all products with optional filtering
     */
    public function index(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $category = $queryParams['category'] ?? null;

        $products = [
            ['id' => 1, 'name' => 'Laptop', 'price' => 999.99, 'category' => 'electronics', 'stock' => 10],
            ['id' => 2, 'name' => 'Mouse', 'price' => 29.99, 'category' => 'electronics', 'stock' => 100],
            ['id' => 3, 'name' => 'Desk', 'price' => 299.99, 'category' => 'furniture', 'stock' => 5],
        ];

        if ($category) {
            $products = array_filter($products, fn($p) => $p['category'] === $category);
        }

        return $this->response($response, array_values($products));
    }

    /**
     * Get a single product
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);

        $products = [
            1 => ['id' => 1, 'name' => 'Laptop', 'price' => 999.99, 'category' => 'electronics', 'stock' => 10, 'description' => 'High-end laptop'],
            2 => ['id' => 2, 'name' => 'Mouse', 'price' => 29.99, 'category' => 'electronics', 'stock' => 100, 'description' => 'Wireless mouse'],
        ];

        if (!isset($products[$id])) {
            return $this->response($response, [], ['Product not found'], 404);
        }

        return $this->response($response, $products[$id]);
    }

    /**
     * Create a new product
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];

        $errors = [];
        if (empty($data['name'])) {
            $errors[] = 'Product name is required';
        }
        if (empty($data['price']) || !is_numeric($data['price'])) {
            $errors[] = 'Valid price is required';
        }
        if (empty($data['category'])) {
            $errors[] = 'Category is required';
        }

        if (!empty($errors)) {
            return $this->response($response, [], $errors, 422);
        }

        $product = [
            'id' => rand(100, 999),
            'name' => $data['name'],
            'price' => (float) $data['price'],
            'category' => $data['category'],
            'stock' => (int) ($data['stock'] ?? 0),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->response($response, $product, [], 201);
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $data = $request->getParsedBody() ?? [];

        if ($id <= 0) {
            return $this->response($response, [], ['Invalid product ID'], 400);
        }

        if (!isset($data['quantity'])) {
            return $this->response($response, [], ['Quantity is required'], 422);
        }

        $product = [
            'id' => $id,
            'stock' => (int) $data['quantity'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->response($response, $product);
    }
}
