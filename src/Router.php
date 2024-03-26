<?php

declare(strict_types=1);

namespace Rukavishnikov\Php\Router;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

final class Router implements RouterInterface
{
    /**
     * @var array
     */
    private array $controllers = [];

    /**
     * @param array $controllers
     */
    public function __construct(array $controllers)
    {
        foreach ($controllers as $pattern => $controller) {
            $this->addController($pattern, $controller);
        }
    }

    /**
     * @inheritDoc
     */
    public function getControllerNameAndParseAttributes(ServerRequestInterface $request, array &$attributes): string
    {
//        $method = $request->getMethod(); // TODO: add method to pattern and check it here
        $path = $request->getUri()->getPath();

        foreach ($this->controllers as $pattern => $controller) {
            $patternOriginalLength = strlen($pattern);

            $pattern = rtrim($pattern, ']'); // Remove trailing ']' from optional parameters

            $optionalCount = $patternOriginalLength - strlen($pattern);

            $pattern = str_replace('[/', '_[/_', $pattern, $replaceCount);

            if ($replaceCount !== $optionalCount) {
                throw new RuntimeException('Route pattern error!', 500);
            }

            $pattern = @preg_replace_callback(
                '/{([a-zA-Z_][a-zA-Z0-9_-]*)(?::(.+?))?}/',
                function ($matches) use (&$attributes) {
                    $attributes[$matches[1]] = null;

                    return '(' . ($matches[2] ?? '.+?') . ')';
                },
                $pattern
            );

            $pattern = str_replace('_[/_', '(?:/', $pattern);
            $pattern .= str_repeat(')?', $optionalCount);

            $pattern = '~^' . $pattern . '$~';

            $pregMatchResult = @preg_match($pattern, $path, $matches);

            if ($pregMatchResult === false) {
                throw new RuntimeException('Route pattern error!', 500);
            }

            if ($pregMatchResult === 1) {
                $i = 1;

                foreach ($attributes as &$value) {
                    $value = $matches[$i] ?? null;

                    $i++;
                }

                return $controller;
            }
        }

        throw new NotFoundException('Page not found!', 404);
    }

    /**
     * @param string $pattern
     * @param string $controller
     * @return void
     */
    private function addController(string $pattern, string $controller): void
    {
        $this->controllers[$pattern] = $controller;
    }
}
