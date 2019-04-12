<?php

declare(strict_types=1);

namespace Aidphp\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class JsonContentMiddleware implements MiddlewareInterface
{
    protected $type  = 'application/json';
    protected $assoc = true;
    protected $depth = 512;
    protected $opts  = 0;

    public function __construct(bool $assoc = true, int $depth = 512, int $opts = 0)
    {
        $this->assoc = $assoc;
        $this->depth = $depth;
        $this->opts  = $opts;
    }

    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        if (0 === stripos($req->getHeaderLine('Content-Type'), $this->type))
        {
            $req = $req->withParsedBody($this->parse($req->getBody()));
        }

        return $handler->handle($req);
    }

    protected function parse(StreamInterface $body)//: array
    {
        $json = $body->__toString();

        if (! $json)
        {
            return [];
        }

        $data = json_decode($json, $this->assoc, $this->depth, $this->opts);

        if (JSON_ERROR_NONE !== json_last_error())
        {
            throw new RuntimeException('Error parsing JSON: ' . json_last_error_msg());
        }

        return $data;
    }
}