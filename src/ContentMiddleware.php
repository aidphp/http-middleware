<?php

declare(strict_types=1);

namespace Aidphp\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ContentMiddleware implements MiddlewareInterface
{
    protected $contentType;

    public function __construct(string $contentType)
    {
        $this->contentType = $contentType;
    }

    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        $res = $handler->handle($req);

        $code = $res->getStatusCode();

        if ($code >= 100 && $code < 200 || in_array($code, [204, 205, 304], true))
        {
            return $res;
        }

        if (! $res->hasHeader('Content-Type'))
        {
            $res = $res->withHeader('Content-Type', $this->contentType);
        }

        if (! $res->hasHeader('Content-Length') && ! $res->hasHeader('Transfer-Encoding') && null !== ($size = $res->getBody()->getSize()))
        {
            $res = $res->withHeader('Content-Length', (string) $size);
        }

        return $res;
    }
}