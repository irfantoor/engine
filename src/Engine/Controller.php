<?php

namespace IrfanTOOR\Engine;

use IrfanTOOR\Collection;
use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Session;
use IrfanTOOR\Engine\View;

class Controller extends Collection
{
    protected $engine;
    protected $middlewares = [];

    public function __construct($engine)
    {
        $this->engine = $engine;
        $this->set('engine', $this->engine);
    }

    function __call($func, array $args)
    {
        try {
            $result = call_user_func_array([$this->engine, $func], $args);
            return $result;
        } catch(Exception $e) {
        }

        throw new Exception("Method: $func, does not exist!", 1);
    }

    function dump($v) {
        Debug::dump($v);
    }

    function engine()
    {
        return $this->get('engine');
    }

    function isLogged()
    {
        return $this->get('logged');
    }
    
    function loggedUser()
    {
        return $this->get('user');
    }

    function addMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    function getMiddlewareList()
    {
        return $this->middlewares;
    }

    public function show($response, $tplt)
    {
        $view = new View($this);
        $response->write($view->process($tplt));

        return $response;
    }
}
