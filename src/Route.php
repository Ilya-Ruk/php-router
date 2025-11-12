<?php

declare(strict_types=1);

namespace Rukavishnikov\Php\Router;

use RuntimeException;

final class Route
{
    /**
     * @var array
     */
    public array $attributes = [];

    /**
     * @param string $method
     * @param string $pattern
     * @param string $handler
     */
    public function __construct(
        public string $method,
        public string $pattern,
        public string $handler,
    ) {
        if (!in_array($method, ['GET', 'HEAD', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'])) {
            throw new RuntimeException(sprintf("Method '%s' not supported!", $method), 500);
        }

        if (!class_exists($handler)) {
            throw new RuntimeException(sprintf("Handler '%s' not found!", $handler), 500);
        }
    }
}
