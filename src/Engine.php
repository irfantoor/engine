<?php

namespace IrfanTOOR;

use Closure;
use IrfanTOOR\Collection;
use IrfanTOOR\Container;
use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Http\Response;

class Engine
{
    protected $config;
    protected $container;
    protected $events;
    protected $middleware_stack = [];
    protected $session;
    protected $default_classes;

    function __construct($config=[])
    {
        $this->default_classes = [
            'cookie'         => 'IrfanTOOR\Engine\Http\Cookie',
            'environment'    => 'IrfanTOOR\Engine\Http\Environment',
            'request'        => 'IrfanTOOR\Engine\Http\Request',
            'response'       => 'IrfanTOOR\Engine\Http\Response',
            'serverrequest'  => 'IrfanTOOR\Engine\Http\ServerRequest',
            'stream'         => 'IrfanTOOR\Engine\Http\Stream',
            'uploaded_file'  => 'IrfanTOOR\Engine\Http\UploadedFile',
            'uri'            => 'IrfanTOOR\Engine\Http\Uri',

            'events'         => 'IrfanTOOR\Engine\Events',
            'router'         => 'IrfanTOOR\Engine\Router',

            'session'        => 'IrfanTOOR\Engine\Session',
        ];

        $this->config = new Collection($config);

        # Set default timezone
        date_default_timezone_set($this->config("timezone", "Europe/Paris"));

        $dl = $this->config('debug.level', 0);
        if ($dl) {
            Debug::enable($dl);
        } else {
            error_reporting(0);
        }

        $this->data = $this->config('data', []);
        $this->container = new Container();
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

        $class = $this->config('classes.' . $method, null);
        if (!$class) {
            $class = $this->default_classes[$method] ?: null;
        }

        if ($class) {
            $class = '\\' . $class;
            if ($method === 'session') {
                $class = new $class($this->serverrequest());
            } else {
                $class = new $class();
            }
            
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
    
    public function config($id, $default = null)
    {
        return $this->config->get($id, $default);
    }
    
    function register($event_id, Closure $event, $level = 10)
    {
        $events = $this->events();
        $events->register($event_id, $event, $level);
    }

    function trigger($event_id)
    {
        $events = $this->events();
        $events->trigger($event_id);
    }

    function redirectTo($url, $status = 307)
    {
        $response = new Response(['status' => $status]);
        $response->get('headers')->set('Location', $url);
        $response->write(sprintf('<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="refresh" content="0;url=%1$s" />
<title>Redirecting to %1$s</title>
</head>
<body>
Redirecting to <a href="%1$s">%1$s</a>.
</body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));

        $response->send();
        exit;
    }

    function run()
    {
        $request  = $this->serverrequest();
        $response = $this->response();
        $router   = $this->router();
        $uri      = $request->get('uri');
        $path     = $uri->get('path');
        $basepath = $uri->get('basepath');
        $args     = explode('/', htmlspecialchars($basepath));

        // extract processed route's $type and $handler
        extract(
            $router->process($request->get('method'), $path)
        );

        switch ($type) {
            case 'closure':
                $response = $handler($request, $response, $args);
                break;

            case 'string':
                if (($pos = strpos($handler, '@')) !== FALSE) {
                    # e.g. process@App\Controller\Main
                    $method = substr($handler, 0, $pos);
                    $cname  = substr($handler, $pos + 1);
                } else {
                    # e.g. App\Controller\Blog
                    $method = 'defaultMethod';
                    $cname  = $handler;
                }
                $class = new $cname($this);

                if (!method_exists($class, $method))
                    $method  = 'defaultMethod';

                $mw_list = $class->getMiddlewareList();
                
                # process middlewares
                foreach($mw_list as $mw) {
                    $mw_class = new $mw($class);
                    $this->middleware_stack[] = $mw_class;
                    $response = $mw_class->process($request, $response, $args);
                }

                # call the contrller method
                $response = $class->$method($request, $response, $args);
                break;

            default:
                $response->set('status', 404);
                $response->write('no route defined!');
        }

        $this->finalize($request, $response, $args)->send();
    }

    public function finalize($request, $response, $args) {
        # finalize middlewares
        while ($this->middleware_stack) {
            $mw_class = array_pop($this->middleware_stack);
            $response = $mw_class->finalize($request, $response, $args);
        }

        return $response;
    }
}
