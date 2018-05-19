<?php

namespace IrfanTOOR\Engine;

class Middleware
{
    # protected $engine;
    protected $controller;

    function __construct($controller)
    {
        $this->controller = $controller;
        # $this->engine = $controller->engine();
    }

    function __call($func, array $args)
    {
        try {
            $result = call_user_func_array([$this->controller, $func], $args);
            return $result;
        } catch(Exception $e) {
        }
    }

    function process($request, $response, $args)
    {
        return $response;
    }

    function finalize($request, $response, $args)
    {
        return $response;
    }
}
