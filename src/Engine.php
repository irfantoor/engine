<?php

namespace IrfanTOOR;

use IrfanTOOR\Collection;
use IrfanTOOR\Container;
use IrfanTOOR\Engine\Debug;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Factory;
use IrfanTOOR\Engine\Http\ServerRequest;
use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Engine\Http\Stream;
use IrfanTOOR\Engine\MiddlewareTrait;
use IrfanTOOR\Engine\Router;

class Engine extends Collection
{
    use Engine\MiddlewareTrait;

    const VERSION = '1.0';

    protected static $instance;
    protected $config;
    protected $container;

    function __construct($config=[])
    {
        static::$instance = $this;

        $this->config = new Collection($config);
        Debug::enable($this->config('debug.level', 0));
        $this->data = $this->config('data', []);

        $this->container = new Container();
    }

    public static function instance()
    {
        return static::$instance;
    }

    public function config($id, $default = null)
    {
        return $this->config->get($id, $default);
    }

    function container() {
        return $this->container;
    }

    public function add($callable)
    {
        return $this->addMiddleware($callable);
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
            $obj = $this->container->get($method);
            if (is_callable($obj)) {
                return call_user_func_array($obj, $args);
            }
        }

        throw new \BadMethodCallException("Method $method is not a valid method");
    }

    function addRoute($method, $path, $handler)
    {
        $router = $this->container['router'];
        $router->addRoute($method, $path, $handler);
    }

    function finalize(Response $response)
    {
        # extract($response->toArray());
        // $stream = $response->getBody();
        // $size = $stream->getSize();
        // if ($size !== null && !$response->hasHeader('Content-Length')) {
        //     $response = $response->withHeader('Content-Length', $size);
        // }

        return $response;
    }

    function run()
    {
        $container = $this->container();

        # todo later in a separate file
        if (!isset($container['router'])) {
            $container['router'] = function() {
                return new Router();
            };
        }

        if (!isset($container['environment'])) {
            $container['environment'] = function() {
                return new Environment();
            };
        }

        if (!isset($container['uri'])) {
            $container['uri'] = function() {
                #$c = Engine::instance()->container();
                $container = $this->container();
                return Uri::createFromEnvironment($container['environment']);
            };
        }

        if (!isset($container['request'])) {
            $container->factory('request', function() {
                #$c = Engine::instance()->container();
                $container = $this->container();
                return new Request($container['environment']);
            });
        }

        if (!isset($container['serverrequest'])) {
            $container->set('serverrequest', function() {
                return new ServerRequest();
            });
        }

        if (!isset($container['request'])) {
            $container['request'] = function() {
                #$c = Engine::instance()->container();
                $container = $this->container();
                return new ServerRequest($container['environment']);
            };
        }

        if (!isset($container['response'])) {
            $container['response'] = function() {
                return new Response();
            };
        }

        $request  = $container['serverrequest'];
        $response = $container['response'];
        $router   = $container['router'];
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

                $next = $this;
                $this->addMiddleware(function($request, $response, $next) use($class, $method, $args){
                    $result = $next($request, $response);
                    $response = $class->$method($request, $result[1], $args);
                    return $response;
                });

                # $response = $class->$method($request, $response, $args);
                break;

            default:
                $stream = $response->getBody();
                $stream->write('no route defined!');
                $response = $response
                    ->withStatus(Response::STATUS_NOT_FOUND);
        }

        $result = $this->callMiddlewares(
            ($request !== null)  ? $request : $this->container['request'],
            ($response !== null) ? $response : $this->container['response']
        );

        $this->finalize($response)->send();
    }

    /**
     * Invoke application
     *
     */
    public function __invoke($request, $response)
    {
        return [$request, $response];
    }
}
