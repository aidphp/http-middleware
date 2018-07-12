<?php

declare(strict_types=1);

namespace Test\Aidphp\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Aidphp\Http\Middleware\ContentMiddleware;

class ContentMiddlewareTest extends TestCase
{
    const CONTENT_TYPE = 'application/octet-stream';

    protected $req;
    protected $res;
    protected $handler;

    public function setUp()
    {
        $this->req  = $this->createMock(ServerRequestInterface::class);
        $this->res = $this->createMock(ResponseInterface::class);

        $this->handler = $this->createMock(RequestHandlerInterface::class);
        $this->handler->expects($this->once())
            ->method('handle')
            ->with($this->req)
            ->willReturn($this->res);
    }

    public function testAddContentType()
    {
        $this->res->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->res->expects($this->once())
            ->method('hasHeader')
            ->with('Content-Type')
            ->willReturn(false);

        $newRes = $this->createMock(ResponseInterface::class);

        $this->res->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', self::CONTENT_TYPE)
            ->willReturn($newRes);

        $newRes->expects($this->once())
            ->method('hasHeader')
            ->with('Content-Length')
            ->willReturn(true);

        $middleware = new ContentMiddleware(self::CONTENT_TYPE);
        $this->assertSame($newRes, $middleware->process($this->req, $this->handler));
    }

    public function testAddContentLength()
    {
        $this->res->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->res->expects($this->exactly(3))
            ->method('hasHeader')
            ->withConsecutive(['Content-Type'], ['Content-Length'], ['Transfer-Encoding'])
            ->willReturn(true, false, false);

        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('getSize')
            ->willReturn(10);

        $this->res->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $newRes = $this->createMock(ResponseInterface::class);

        $this->res->expects($this->once())
            ->method('withHeader')
            ->with('Content-Length', '10')
            ->willReturn($newRes);

        $middleware = new ContentMiddleware(self::CONTENT_TYPE);
        $this->assertSame($newRes, $middleware->process($this->req, $this->handler));
    }

    public function testNoContentLengthIfTransferEncoding()
    {
        $this->res->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->res->expects($this->exactly(3))
            ->method('hasHeader')
            ->withConsecutive(['Content-Type'], ['Content-Length'], ['Transfer-Encoding'])
            ->willReturn(true, false, true);

        $this->res->expects($this->never())
            ->method('withHeader');

        $middleware = new ContentMiddleware(self::CONTENT_TYPE);
        $this->assertSame($this->res, $middleware->process($this->req, $this->handler));
    }

    public function testNoContentLengthIfEmptyBody()
    {
        $this->res->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->res->expects($this->exactly(3))
            ->method('hasHeader')
            ->withConsecutive(['Content-Type'], ['Content-Length'], ['Transfer-Encoding'])
            ->willReturn(true, false, false);

        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('getSize')
            ->willReturn(null);

        $this->res->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $this->res->expects($this->never())
            ->method('withHeader');

        $middleware = new ContentMiddleware(self::CONTENT_TYPE);
        $this->assertSame($this->res, $middleware->process($this->req, $this->handler));
    }

    /**
     * @dataProvider getStatusCode
     */
    public function testProcessWithEmptyResponse(int $statusCode)
    {
        $this->res->expects($this->once())
            ->method('getStatusCode')
            ->willReturn($statusCode);

        $this->res->expects($this->never())
            ->method('withHeader');

        $middleware = new ContentMiddleware(self::CONTENT_TYPE);
        $this->assertSame($this->res, $middleware->process($this->req, $this->handler));
    }

    public function getStatusCode()
    {
        return [
            [100],
            [101],
            [102],
            [204],
            [205],
            [304]
        ];
    }
}