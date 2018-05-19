<?php

namespace IrfanTOOR\Engine;

use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Session;
use IrfanTOOR\Engine\View;

class Controller
{
    protected $engine;
    protected $middlewares;

    public function __construct($engine)
    {
        $this->engine = $engine;
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
        return $this->engine;
    }

    function isLogged()
    {
        $session = $this->session();
        return $session->has('logged');
    }

    function addMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    function getMiddlewareList()
    {
        return $this->middlewares;
    }

    public function show($response, $tplt, $data = [])
    {
        # received data
        $rdata = $data;

        # merge with the config data
        $data = $this->config('data', []);
        $data['engine'] = $this->engine;
        
        foreach($rdata as $k=>$v) {
            $vv = $data[$k] ?: null;
            if (is_array($vv)) {
                $data[$k] = array_merge($vv, $v);
            } else {
                $data[$k] = $v;
            }
        }
        
        $view = new View($this);
        $response->write($view->process($tplt, $data));

        return $response;
    }
}
