<?php

declare(strict_types=1);

namespace Rukavishnikov\Php\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return Route
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function getRoute(ServerRequestInterface $request): Route;
}
