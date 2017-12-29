<?php

namespace IrfanTOOR;

use IrfanTOOR\Collection;
use IrfanTOOR\Container;
use IrfanTOOR\Engine\Debug;
use IrfanTOOR\Engine\Http\Cookies;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Http\ResponseStatus;
use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Engine\Middleware;
use IrfanTOOR\Engine\Router;

class Engine
{
    # use MiddlewareAwareTrait;

    /**
     * Singleton instance
     *
     * @var string
     */
    protected static
        $instance;

    /**
     * Current version
     *
     * @var string
     */
    const VERSION = '1.0';


    /**
     * Configuration
     *
     * @var Collection
     */
    protected $config;

    protected $data;

    /**
     * Container
     *
     * @var Container
     */
    private $container;

    function __construct($config=[])
    {
        static::$instance = $this;

        $this->config = new Collection($config);
        Debug::enable($this->config('debug.level', 0));
        $this->data = $this->config('data', []);

        $c = $this->container = new Container();

        # todo later in a separate file
        $c['router']      = function() {
            return new Router();
        };

        $c['environment'] = function() {
            return new Environment();
        };

        $c['uri']         = function() {
            $c = Engine::instance()->container();
            return Uri::createFromEnvironment($c['environment']);
        };

        $c['cookies']     = function() {
            return new Cookies($_COOKIE);
        };

        $c['request']     = function() {
            return Request::createFromEnvironment();
        };

        $c['response']    = function() {
            return new Response();
        };
    }

    static function instance()
    {
        return static::$instance;
    }

    function config($id, $default = null)
    {
        return $this->config->get($id, $default);
    }

    function data()
    {
        return $this->data;
    }

    function container() {
        return $this->container;
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
        $r = $this->container->get('router');
        $r->addRoute($method, $path, $handler);
    }

    function finalize(Response $response)
    {
        extract($response->toArray());

        $size = mb_strlen($body);
        if ($size !== null && !$headers->has('Content-Length')) {
            # $headers->set('Content-Length', (string) $size);
            $response = $response->with('headers', $headers);
        }

        return $response;
    }

    function run()
    {
        $request  = $this->container['request'];
        $response = $this->container['response'];
        $router   = $this->container['router'];

        $uri      = $request['uri'];
        $path     = $uri['base_path'];

        if ($path === '/')
            $args = [];
        else
            $args = explode('/', htmlspecialchars($path));

        $route    = $router->process($request['method'], $path);
        extract($route);

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
                $response = $response
                    ->with('status', ResponseStatus::STATUS_NOT_FOUND)
                    ->with('body', 'no route defined!');
        }

        $this->finalize($response)->send();
    }
}
