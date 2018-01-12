<?php

namespace IrfanTOOR\Engine\Http\Factory;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use IrfanTOOR\Engine\Http\Uri;
use Interop\Http\Factory\UriFactoryInterface;

class UriFactory implements UriFactoryInterface
{
    /**
     * Create a new URI.
     *
     * @param string $uri
     *
     * @return UriInterface
     *
     * @throws \InvalidArgumentException
     *  If the given URI cannot be parsed.
     */
    public function createUri($uri = '')
    {
        return new Uri($uri);
    }
}
