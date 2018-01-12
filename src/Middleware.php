<?php

namespace IrfanTOOR\Engine;

use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\Response;

class Middleware {
    use MiddlewareTrait;

    public function __construct()
    {
        // Your contruction code
    }

    public function __invoke(Request $request, Response $response, $next=null)
    {
        throw new Exception('__invoke must be defined in derived class');

        // return $response;
    }
}
