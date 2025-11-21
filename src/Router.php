<?php

declare(strict_types=1);

namespace Rukavishnikov\Php\Router;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

final class Router implements RouterInterface
{
    /**
     * @var Route[]
     */
    private array $routeList;

    /**
     * @param Route[] $routeList
     */
    public function __construct(array $routeList)
    {
        foreach ($routeList as $route) {
            $this->addRoute($route);
        }
    }

    /**
     * @inheritDoc
     */
    public function getRoute(ServerRequestInterface $request): Route
    {
        $requestMethod = $request->getMethod();
        $requestPath = urldecode($request->getUri()->getPath());

        foreach ($this->routeList as $route) {
            /**
             * /hello - without any params
             * /hello/{name:[a-zA-Z][a-zA-Z-]*} - without optional params (name required)
             * /hello/{name:[a-zA-Z][a-zA-Z-]*}/{id:\d+} - without optional params (name and id required)
             * /hello/{name:[a-zA-Z][a-zA-Z-]*}[/{id:\d+}] - with one optional parameter (name required, id optional)
             * /hello[/{name:[a-zA-Z][a-zA-Z-]*}][/{id:\d+}] - with two optional params (name and id optional)
             */

            $pattern = @preg_replace_callback(
                '~(\[)?/\{([a-zA-Z_][a-zA-Z0-9_-]*)(?::(.+?))?}(])?~',
                static function (array $matches) {
                    if (!empty($matches[1]) && !empty($matches[4])) { // Optional parameter
                        return '(/(?P<' . $matches[2] . '>' . ($matches[3] ?? '.+?') . '))?';
                    } else { // Required parameter
                        return '/(?P<' . $matches[2] . '>' . ($matches[3] ?? '.+?') . ')';
                    }
                },
                $route->pattern
            );

            $pattern = '~^' . $pattern . '$~';

            $pregMatchResult = @preg_match($pattern, $requestPath, $matches);

            if ($pregMatchResult === false) {
                throw new RuntimeException('Route pattern error!');
            }

            if ($pregMatchResult === 0) {
                continue;
            }

            if ($requestMethod !== $route->method) {
                throw new MethodNotAllowedException('Method not allowed!');
            }

            $route->attributes = [];

            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $route->attributes[$key] = $value;
                }
            }

            return $route;
        }

        throw new RouteNotFoundException('Route not found!');
    }

    /**
     * @param Route $route
     * @return void
     */
    private function addRoute(Route $route): void
    {
        $this->routeList[] = $route;
    }
}
