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
            foreach ($res->getHeaders() as $name => $values)
            {
                foreach ($values as $value)
                {
                    header($name . ': ' . $value, false);
                }
            }

            $code = $res->getStatusCode();
            $text = $res->getReasonPhrase();

            header('HTTP/' . $res->getProtocolVersion() . ' ' . $code . ($text ? ' ' . $text : ''), true, $code);
        }

        echo $res->getBody()->__toString();

        return $res;
    }
}