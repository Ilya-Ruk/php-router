<?php

declare(strict_types=1);

namespace Rukavishnikov\Php\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param array $attributes
     * @return string
     * @throws NotFoundException
     */
    public function getControllerNameAndParseAttributes(ServerRequestInterface $request, array &$attributes): string;
}
