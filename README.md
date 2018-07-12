# Aidphp Http Middlewares

This package include the following Relay-compatible middleware:

- _ContentMiddleware_ inject the Content-Type and Content-Length header into the response based on the body size
- _SenderMiddleware_ to send a PSR-7 `ResponseInterface` headers and body.