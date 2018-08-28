<?php

namespace IrfanTOOR;

use IrfanTOOR\Collection;
use IrfanTOOR\Container;
use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Exception;

class Engine
{
    protected $config;
    protected $container;

    /*
     * Constructs the Irfan's Engine
     *
     */
    function __construct($config=[])
    {
        $this->config    = new Collection($config);
        $this->container = new Container();

        # Set default timezone
        date_default_timezone_set($this->config("timezone", "Europe/Paris"));
        
        # Sets the debug level of engine
        $dl = $this->config('debug.level', 0);
        if ($dl) {
            Debug::enable($dl);
            if ($this->config('exception.log.enabled')) {
                Exception::log($this->config('exception.log.file'));
            }
        } else {
            error_reporting(0);
        }
    }

    /**
     * Calling a non-existant method on Engine checks to see if there's an item
     * in the container returns it or returns a class of the same name.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if ($this->container->has($method)) {
            $obj = $this->container[$method];
            if (is_callable($obj)) {
                return call_user_func_array($obj, $args);
            } else {
                return $obj;
            }
        }

        $class = '\\IrfanTOOR\\Engine\\Http\\' . $method;
        $class = new $class();
        $this->container->set($method, $class);
        return $class;
    }

    /**
     * Returns the config element
     *
     * @param string $id
     * @param mixed  $default
     *
     * @return mixed
     */
    public function config($id, $default = null)
    {
        return $this->config->get($id, $default);
    }
    
    /**
     * Runs the engine, the processes the request
     *
     */
    function run()
    {
        $request  = $this->ServerRequest();
        $response = $this->Response();
        $uri      = $request->getUri();
        $basepath = $uri->getBasePath();
        $args     = explode('/', htmlspecialchars($basepath));

        $response = $this->process($request, $response, $args);

        $this->finalize($request, $response, $args);
    }
    
    /**
     * Process on request and/or passed arguments and returns response
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param Array                  $args
     *
     * @return ResponseInterface $response
     */
    function process($request, $response, $args)
    {
        throw new Exception('function: "process", does not exist in the derived class');
    }
    
    /**
     * Finalizes the response and sends it
     *
     * @param Request  $request
     * @param Response $response
     * @param Array    $args
     */    
    function finalize($request, $response, $args)
    {
        # any final processing
        # ...
        
        $response->send();
    }
}
