<?php

declare(strict_types=1);

namespace Aidphp\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class FormContentMiddleware implements MiddlewareInterface
{
    protected $type = 'application/x-www-form-urlencoded';

    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        if (0 === stripos($req->getHeaderLine('Content-Type'), $this->type))
        {
            $req = $req->withParsedBody($this->parse($req->getBody()));
        }

        return $handler->handle($req);
    }

    protected function parse(StreamInterface $body): array
    {
        $data = [];

        parse_str($body->__toString(), $data);

        return $data;
    }
}