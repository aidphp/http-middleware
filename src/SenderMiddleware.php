<?php

declare(strict_types=1);

namespace Aidphp\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class SenderMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        $res = $handler->handle($req);

        if (! headers_sent())
        {
            $code   = $res->getStatusCode();
            $phrase = $res->getReasonPhrase();

            header('HTTP/' . $res->getProtocolVersion() . ' ' . $code . ($phrase ? ' ' . $phrase : ''), true, $code);

            foreach ($res->getHeaders() as $name => $values)
            {
                foreach ($values as $value)
                {
                    header($name . ': ' . $value, false);
                }
            }
        }

        echo (string) $res->getBody();

        return $res;
    }
}