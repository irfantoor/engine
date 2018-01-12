<?php

namespace IrfanTOOR\Engine\Http\Factory;

use Interop\Http\Factory\ResponseFactoryInterface;
use IrfanTOOR\Engine\Http\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * Create a new response.
     *
     * @param integer $code HTTP status code
     *
     * @return ResponseInterface
     */
    public function createResponse(
        $code = 200
    ) : ResponseInterface
    {
        return (new Response())
                ->withStatus($code);
    }
}
