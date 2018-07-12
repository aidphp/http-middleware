<?php

declare(strict_types=1);

namespace Test\Aidphp\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Aidphp\Http\Middleware\SenderMiddleware;

class SenderMiddlewareTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testProcess()
    {
        $content = 'Hello World!';

        $req = $this->createMock(ServerRequestInterface::class);

        $res = $this->createMock(ResponseInterface::class);
        $res->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $res->expects($this->once())
            ->method('getReasonPhrase')
            ->willReturn('OK');

        $res->expects($this->once())
            ->method('getProtocolVersion')
            ->willReturn('1.1');

        $res->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Content-Type' => ['text/plain'], 'Content-Length' => [strlen($content)]]);

        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn($content);

        $res->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($req)
            ->willReturn($res);

        $sender = new SenderMiddleware();
        ob_start();
        $this->assertSame($res, $sender->process($req, $handler));
        $body = ob_get_clean();
        $this->assertContains($content, $body);
    }
}