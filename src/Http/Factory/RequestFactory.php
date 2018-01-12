<?php

namespace IrfanTOOR\Engine\Http\Factory;

use Interop\Http\Factory\RequestFactoryInterface;
use IrfanTOOR\Engine\Http\Factory;
use IrfanTOOR\Engine\Http\Request;
use Psr\Http\Message\RequestInterface;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * Create a new request.
     *
     * @param string $method
     * @param UriInterface|string $uri
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri) : RequestInterface
    {
        return (new Request())
                    ->withMethod($method)
                    ->withUri(Factory::createUri($uri));
    }
}
