<?php

namespace IrfanTOOR;

use IrfanTOOR\Collection;
use IrfanTOOR\Container;
use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Http\ServerRequest;
use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Router;

class Engine extends Collection
{
    const VERSION = '1.0';

    protected static $instance;
    protected $initialized;
    protected $config;
    protected $container;

    function __construct($config=[])
    {
        static::$instance = $this;

        $this->config = new Collection($config);

        $dl = $this->config('debug.level', 0);
        if ($dl) {
            Debug::enable($dl);
        } else {
            error_reporting(0);
        }

        $this->data = $this->config('data', []);
        $this->container = new Container();
    }

    public function config($id, $default = null)
    {
        return $this->config->get($id, $default);
    }

    /**
     * Calling a non-existant method on App checks to see if there's an item
     * in the container that is callable and if so, calls it.
     *
     * @param  string $method
     * @param  array $args
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
        # set container
        $class = $this->config('classes.' . $method, null);
        if ($class) {
            #throw new \BadMethodCallException("$class");
            $class = '\\' . $class;
            $class = new $class;
            $this->container->set($method, $class);
            return $class;
        }

        throw new \BadMethodCallException("Method $method is not a valid method");
    }

    function addRoute($method, $path, $handler)
    {
        $router = $this->router();
        $router->addRoute($method, $path, $handler);
    }

    function run()
    {
        $request  = $this->serverrequest();
        $response = $this->response();
        $router   = $this->router();
        $uri      = $request->getUri();
        $path     = $uri->getPath();
        $basepath = rtrim(ltrim($path, '/'), '/');
        $args     = explode('/', htmlspecialchars($basepath));

        // extract processed route
        extract(
            $router->process($request->getMethod(), $path)
        );

        switch ($type) {
            case 'closure':
                $response = $handler($request, $response, $args);
                break;

            case 'string':
                if (($pos = strpos($handler, '@')) !== FALSE) {
                    $method = substr($handler, 0, $pos);
                    $cname  = substr($handler, $pos + 1);
                } else {
                    $method = 'defaultMethod';
                    $cname  = $handler;
                }
                $class = new $cname($this);

                if (!method_exists($class, $method))
                    $method  = 'defaultMethod';

                $response = $class->$method($request, $response, $args);
                break;

            default:
                $stream = $response->getBody();
                $stream->write('no route defined!');
                $response = $response
                    ->withStatus(Response::STATUS_NOT_FOUND);
        }

        $this->finalize($request, $response, $args)->send();
    }

    function finalize($request, $response, $args)
    {
        # This function can be overriden in the extended classes
        # to do some finalization like logging etc.
        return $response;
    }
}
