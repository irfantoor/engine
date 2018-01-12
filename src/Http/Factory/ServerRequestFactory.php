<?php

namespace IrfanTOOR\Engine\Http\Factory;

use Interop\Http\Factory\ServerRequestFactoryInterface;
use IrfanTOOR\Engine\Http\Factory;
use IrfanTOOR\Engine\Http\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * Create a new server request.
     *
     * @param string $method
     * @param UriInterface|string $uri
     *
     * @return ServerRequestInterface
     */
    public function createServerRequest(
        $method,
        $uri
    ) : ServerRequestInterface
    {
        return ($this->createServerRequestFromArray([]))
                    ->withMethod($method)
                    ->withUri(Factory::createUri($uri));
    }

    /**
     * Create a new server request from server variables.
     *
     * @param array $server Typically $_SERVER or similar structure.
     *
     * @return ServerRequestInterface
     *
     * @throws \InvalidArgumentException
     *  If no valid method or URI can be determined.
     */
    public function createServerRequestFromArray(
        array $server
    ) : ServerRequestInterface
    {
        return new ServerRequest($server);
    }
}
