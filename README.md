# Aidphp Http Middlewares

[![Build Status](https://scrutinizer-ci.com/g/aidphp/http-middleware/badges/build.png?b=master)](https://scrutinizer-ci.com/g/aidphp/http-middleware/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aidphp/http-middleware/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aidphp/http-middleware/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/aidphp/http-middleware/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/aidphp/http-middleware/?branch=master)
[![Build Status](https://travis-ci.org/aidphp/http-middleware.svg?branch=master)](https://travis-ci.org/aidphp/http-middleware)

This package include the following Relay-compatible middleware:

- _ContentMiddleware_ inject the Content-Type and Content-Length header into a PSR-7 `ResponseInterface` headers.
- _SenderMiddleware_ to send a PSR-7 `ResponseInterface` headers and body.