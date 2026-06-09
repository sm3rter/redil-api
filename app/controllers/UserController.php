<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Facades\Request as RequestFacade;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index(Request $request, Response $response): Response
    {
        $users = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];

        return $this->response($response, $users);
    }

    /**
     * Get a single user by ID
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);

        if ($id <= 0) {
            return $this->response($response, [], ['Invalid user ID'], 400);
        }

        $user = ['id' => $id, 'name' => 'User ' . $id, 'email' => 'user' . $id . '@example.com'];

        return $this->response($response, $user);
    }

    /**
     * Create a new user
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];

        // Basic validation
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }

        if (!empty($errors)) {
            return $this->response($response, [], $errors, 422);
        }

        $user = [
            'id' => rand(100, 999),
            'name' => $data['name'],
            'email' => $data['email'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->response($response, $user, [], 201);
    }

    /**
     * Update a user
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $data = $request->getParsedBody() ?? [];

        if ($id <= 0) {
            return $this->response($response, [], ['Invalid user ID'], 400);
        }

        $user = [
            'id' => $id,
            'name' => $data['name'] ?? 'User ' . $id,
            'email' => $data['email'] ?? 'user' . $id . '@example.com',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->response($response, $user);
    }

    /**
     * Delete a user
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);

        if ($id <= 0) {
            return $this->response($response, [], ['Invalid user ID'], 400);
        }

        return $this->response($response, ['message' => 'User deleted'], [], 200);
    }

    /**
     * Login endpoint
     */
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];

        if (empty($data['email']) || empty($data['password'])) {
            return $this->response($response, [], ['Email and password required'], 422);
        }

        $token = bin2hex(random_bytes(32));

        return $this->response($response, ['token' => $token, 'expires_in' => 3600]);
    }
}
