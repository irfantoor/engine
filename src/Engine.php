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
    
//     protected $middleware_stack = [];
//     protected $session;
    protected static $default_classes = [
        'Cookie'         => 'IrfanTOOR\Engine\Http\Cookie',
        'Environment'    => 'IrfanTOOR\Engine\Http\Environment',
        'Request'        => 'IrfanTOOR\Engine\Http\Request',
        'Response'       => 'IrfanTOOR\Engine\Http\Response',
        'ServerRequest'  => 'IrfanTOOR\Engine\Http\ServerRequest',
        'Stream'         => 'IrfanTOOR\Engine\Http\Stream',
        'UploadedFile'   => 'IrfanTOOR\Engine\Http\UploadedFile',
        'Uri'            => 'IrfanTOOR\Engine\Http\Uri',
    ];

    function __construct($config=[])
    {
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
            $class = self::$default_classes[$method] ?: null;
        }

        if ($class) {
            $class = '\\' . $class;
            $class = new $class();            
            $this->container->set($method, $class);
            return $class;
        }

        throw new \BadMethodCallException("Method $method is not a valid method");
    }

    public function config($id, $default = null)
    {
        return $this->config->get($id, $default);
    }
    
    function init()
    {
        $request  = $this->ServerRequest();
        $uri      = $request->getUri();
        $basepath = $uri->getBasePath();
        $args     = explode('/', htmlspecialchars($basepath));
        $this->container->set('args', $args);
    }
}
