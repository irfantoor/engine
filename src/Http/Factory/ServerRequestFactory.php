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
        $server_request = $this->createServerRequestFromArray([]);
        if ($method !== null)
            $server_request = $server_request->withMethod($method);

        if ($uri !== null)
            $server_request = $server_request->withUri(Factory::createUri($uri));

        return $server_request;
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
        $env = new Environment($server);

        // Headers from environment
        $data = [];
        foreach($env as $k=>$v) {
            $k = strtoupper($k);
            if (strpos($k, 'HTTP_') === 0) {
                $k = substr($k, 5);
            } else {
                if (!isset(static::$special[$k]))
                    continue;
            }

            // normalize key
            $k = str_replace(
                ' ',
                '-',
                ucwords(strtolower(str_replace('_', ' ', $k)))
            );

            $data[$k] = $v;
        }

        return new Headers($data);

        $sr = new ServerRequest();
        $sr = $sr
            ->withVersion(str_replace('HTTP/', '', $env['SERVER_PROTOCOL']))
            ->withMethod($env['REQUEST_METHOD'])
            ->withUri(Factory::createUriFromEnvironment($env))
            ->withHeaders()

        $this->server  = $env->toArray();
        $this->cookies = $_COOKIE;
        $this->query   = $_GET;
        $this->files   = UploadedFile::createFromEnvironment($_FILES);
        $this->post    = $_POST;

        // create an array of attributes
        $this->attributes = array_merge(
            ($_FILES ?: []),
            ($_GET ?: []),
            ($_POST ?: []),
            ($_COOKIE ?: [])
        );
        return new ServerRequest($server);
    }
}
