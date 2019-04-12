<?php

declare(strict_types=1);

namespace Test\Aidphp\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\StreamInterface;
use Aidphp\Http\Middleware\FormContentMiddleware;

class FormContentMiddlewareTest extends TestCase
{
    public function testProcessWithNonFormContentType()
    {
        $req = $this->createMock(ServerRequestInterface::class);
        $req->expects($this->once())
            ->method('getHeaderLine')
            ->with('Content-Type')
            ->willReturn('');

        $res = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($req)
            ->willReturn($res);

        $middleware = new FormContentMiddleware();
        $this->assertSame($res, $middleware->process($req, $handler));
    }

    /**
     * @dataProvider getContent
     */
    public function testProcess(string $content, array $result)
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn($content);

        $req = $this->createMock(ServerRequestInterface::class);
        $req->expects($this->once())
            ->method('getHeaderLine')
            ->with('Content-Type')
            ->willReturn('application/x-www-form-urlencoded');

       $req->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

       $req->expects($this->once())
            ->method('withParsedBody')
            ->with($result)
            ->willReturn($this->createMock(ServerRequestInterface::class));

        $res = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($req)
            ->willReturn($res);

        $middleware = new FormContentMiddleware();
        $this->assertSame($res, $middleware->process($req, $handler));
    }

    public function getContent()
    {
        return [
            ['', []],
            ['bar=foo', ['bar' => 'foo']],
        ];
    }
}