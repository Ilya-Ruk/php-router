<?php

declare(strict_types=1);

namespace Rukavishnikov\Php\Router;

use RuntimeException;

final class Route
{
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PATCH = 'PATCH';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * @var string[]
     */
    private static array $supportedMethod = [
        self::METHOD_GET,
        self::METHOD_HEAD,
        self::METHOD_POST,
        self::METHOD_PATCH,
        self::METHOD_PUT,
        self::METHOD_DELETE,
        self::METHOD_OPTIONS,
    ];

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
        if (!in_array($method, self::$supportedMethod)) {
            throw new RuntimeException(sprintf("Method '%s' not supported!", $method), 500);
        }

        if (!class_exists($handler)) {
            throw new RuntimeException(sprintf("Handler '%s' not found!", $handler), 500);
        }
    }
}
