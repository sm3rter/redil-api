<?php

use Slim\Psr7\Response as SlimResponse;

// ── JSON Responses ────────────────────────────────────────────────────────────

if (!function_exists('json_response')) {
    /**
     * Build a standard JSON response.
     *
     * @param SlimResponse $response
     * @param mixed        $data
     * @param int          $status
     * @param array        $headers
     */
    function json_response(SlimResponse $response, mixed $data, int $status = 200, array $headers = []): SlimResponse
    {
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);

        $response = $response->withStatus($status)->withHeader('Content-Type', 'application/json');

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }
}

if (!function_exists('api_success')) {
    /**
     * Standard API success envelope.
     *
     * json: { "success": true, "data": ..., "message": "..." }
     */
    function api_success(SlimResponse $response, mixed $data = null, string $message = 'OK', int $status = 200): SlimResponse
    {
        return json_response($response, [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }
}

if (!function_exists('api_created')) {
    /**
     * 201 Created response.
     */
    function api_created(SlimResponse $response, mixed $data = null, string $message = 'Created'): SlimResponse
    {
        return api_success($response, $data, $message, 201);
    }
}

if (!function_exists('api_error')) {
    /**
     * Standard API error envelope.
     *
     * json: { "success": false, "message": "...", "errors": [...] }
     */
    function api_error(SlimResponse $response, string $message, int $status = 400, array $errors = []): SlimResponse
    {
        $body = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $body['errors'] = $errors;
        }

        return json_response($response, $body, $status);
    }
}

if (!function_exists('api_not_found')) {
    function api_not_found(SlimResponse $response, string $message = 'Resource not found'): SlimResponse
    {
        return api_error($response, $message, 404);
    }
}

if (!function_exists('api_unauthorized')) {
    function api_unauthorized(SlimResponse $response, string $message = 'Unauthorized'): SlimResponse
    {
        return api_error($response, $message, 401);
    }
}

if (!function_exists('api_forbidden')) {
    function api_forbidden(SlimResponse $response, string $message = 'Forbidden'): SlimResponse
    {
        return api_error($response, $message, 403);
    }
}

if (!function_exists('api_validation_error')) {
    /**
     * 422 Unprocessable Entity — validation failures.
     *
     * $errors format: ['field' => ['message', ...], ...]
     */
    function api_validation_error(SlimResponse $response, array $errors, string $message = 'Validation failed'): SlimResponse
    {
        return json_response($response, [
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }
}

if (!function_exists('api_server_error')) {
    function api_server_error(SlimResponse $response, string $message = 'Internal Server Error'): SlimResponse
    {
        return api_error($response, $message, 500);
    }
}

if (!function_exists('api_no_content')) {
    /**
     * 204 No Content — used after DELETE.
     */
    function api_no_content(SlimResponse $response): SlimResponse
    {
        return $response->withStatus(204);
    }
}

if (!function_exists('api_paginate')) {
    /**
     * Wrap a paginated result set in the standard envelope.
     *
     * @param SlimResponse $response
     * @param array        $items       Current page items
     * @param int          $total       Total records
     * @param int          $page        Current page (1-based)
     * @param int          $perPage     Items per page
     * @param string       $message
     */
    function api_paginate(
        SlimResponse $response,
        array $items,
        int $total,
        int $page,
        int $perPage,
        string $message = 'OK'
    ): SlimResponse {
        $lastPage = max(1, (int) ceil($total / $perPage));

        return json_response($response, [
            'success' => true,
            'message' => $message,
            'data'    => $items,
            'meta'    => [
                'total'       => $total,
                'per_page'    => $perPage,
                'current_page'=> $page,
                'last_page'   => $lastPage,
                'from'        => $total === 0 ? null : ($page - 1) * $perPage + 1,
                'to'          => $total === 0 ? null : min($page * $perPage, $total),
            ],
        ]);
    }
}

// ── Request Helpers ───────────────────────────────────────────────────────────

if (!function_exists('bearer_token')) {
    /**
     * Extract Bearer token from Authorization header.
     */
    function bearer_token(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}

if (!function_exists('request_ip')) {
    /**
     * Get the real client IP (proxy-aware).
     */
    function request_ip(): string
    {
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                return explode(',', $_SERVER[$key])[0];
            }
        }
        return '0.0.0.0';
    }
}

if (!function_exists('is_json_request')) {
    function is_json_request(): bool
    {
        $accept      = $_SERVER['HTTP_ACCEPT']       ?? '';
        $contentType = $_SERVER['CONTENT_TYPE']      ?? '';
        return str_contains($accept, 'application/json') || str_contains($contentType, 'application/json');
    }
}

// ── Validation Helpers ────────────────────────────────────────────────────────

if (!function_exists('validate')) {
    /**
     * Simple field validator. Returns array of errors or empty array if valid.
     *
     * Rules: required | min:N | max:N | email | numeric | in:a,b,c | regex:/pattern/
     *
     * Usage:
     *   $errors = validate($body, [
     *       'email'    => 'required|email',
     *       'password' => 'required|min:8',
     *       'role'     => 'required|in:admin,user',
     *   ]);
     */
    function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value      = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                [$ruleName, $ruleParam] = array_pad(explode(':', $rule, 2), 2, null);

                if ($ruleName === 'required') {
                    if ($value === null || $value === '') {
                        $errors[$field][] = "The {$field} field is required.";
                    }
                    continue;
                }

                if ($value === null || $value === '') continue;

                match ($ruleName) {
                    'min'     => strlen((string) $value) < (int) $ruleParam
                                    && ($errors[$field][] = "The {$field} must be at least {$ruleParam} characters."),
                    'max'     => strlen((string) $value) > (int) $ruleParam
                                    && ($errors[$field][] = "The {$field} may not be greater than {$ruleParam} characters."),
                    'email'   => !filter_var($value, FILTER_VALIDATE_EMAIL)
                                    && ($errors[$field][] = "The {$field} must be a valid email address."),
                    'numeric' => !is_numeric($value)
                                    && ($errors[$field][] = "The {$field} must be a number."),
                    'in'      => !in_array($value, explode(',', $ruleParam), true)
                                    && ($errors[$field][] = "The {$field} must be one of: {$ruleParam}."),
                    'regex'   => !preg_match($ruleParam, (string) $value)
                                    && ($errors[$field][] = "The {$field} format is invalid."),
                    default   => null,
                };
            }
        }

        return $errors;
    }
}
