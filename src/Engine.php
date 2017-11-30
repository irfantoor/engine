<?php

namespace IrfanTOOR;

use IrfanTOOR\Debug;
use IrfanTOOR\Exception;
use IrfanTOOR\Request;
use IrfanTOOR\Response;
use IrfanTOOR\Ruotes;
use IrfanTOOR\Uri;

class Engine extends Collection
{
    const
        NAME    = "Irfan's Engine",
        VERSION = '0.1';

    protected static $instance;

    protected
        $config,
        $env,
        $router;

    function __construct($config = [])
    {
        self::$instance = $this;

        error_reporting(0);
        register_shutdown_function([$this, 'shutdown']);

        $this->config = new Collection($config);
        $this->config->lock();

        date_default_timezone_set($this->config('timezone', 'Europe/Paris'));

        if (($dl = $this->config['debug']['level'] ?: 0)) {
            Debug::enable($dl);
        }

        ob_start();
        #Autoload::register();
        $this->env = new Environment($this->config('env', []));
    }

    function config($key, $default=null)
    {
        return $this->config->get($key, $default);
    }

    function getRouter() {
        if (!$this->router)
            $this->router = new Router();

        return $this->router;
    }

    function setRouter($router) {
        $this->router = $router;
    }

    function addRoute($method, $patern, $callable) {
        $router = $this->getRouter();
        $router->add($method, $patern, $callable);
    }

    function run()
    {
        # Initialize
        $request  = new Request($this->env);
        $response = new Response();
        $uri = $request['uri'];

        # Process
        $base_path = ltrim(rtrim($uri['path'], '/'), '/') ?: '/';
		$method = $request['method'];
        $match = $this->getRouter()->process($method, $uri);

        $callable = $match['callable'];

        switch ($match['type']) {
            case 'closure':
                $response = $callable($request, $response);
                break;

            case 'string':
                if (($pos = strpos($callable, '@')) !== FALSE) {
                    $method = substr($callable, 0, $pos);
                    $controller = substr($callable, $pos+1);
                } else {
                    $method = 'default_method';
                    $controller = $callable;
                }
                $c = new $controller();

                if (!method_exists($c, $method))
                    $method  = 'default_method';

                $response = $c->$method($request, $response);
                break;

            default:
                $response['status'] = 404;
                break;
        }

        $response->send();
        die();
    }

    function shutdown()
    {
        if (ob_get_level())
            ob_flush();

        if ($this->config['debug']['level']) {
            Debug::banner();
            if (Debug::level()>2){
                $this->getRouter()->dump();
                Debug::table($this->env->toArray(),['Keys', 'Values'], 'Environment');
            }
        }
    }
}
