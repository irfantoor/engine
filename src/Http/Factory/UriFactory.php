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

    public function createUriFromEnvironment($env = [])
    {
        if (!($env instanceof Environment)) {
            $env = new Environment($env);
        }

        $host = $env['HTTP_HOST'] ?: ($env['SERVER_NAME'] ?: 'localhost');
        $protocol = $env['SERVER_PROTOCOL'] ?: 'HTTP/1.1';
        $pos = strpos($protocol, '/');
        $ver = substr($protocol, $pos + 1);
        $url = ($env['REQUEST_SCHEME'] ?: 'http') .
                '://' .
                $host .
                ($env['REQUEST_URI'] ?: '/');

        return $this->createUri($url);
    }
}
