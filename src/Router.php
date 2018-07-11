<?php

namespace IrfanTOOR\Engine;

use InvalidArgumentException;
use IrfanTOOR\Collection;
use IrfanTOOR\Exception;
use IrfanTOOR\Engine\Http\Request;

/**
 * Router
 */
class Router extends Collection
{
    protected $strict_mode = false;
    protected static $methods = [
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'PURGE',
        'OPTIONS',
        'TRACE',
        'CONNECT',
    ];

    public function __construct()
    {
        foreach (self::$methods as $method)
            $this->set($method, []);
    }

    public function setAllowedMethods($methods = null)
    {
        if (!$methods) {
            $this->strict_mode = false;
            $methods = self::$methods;
        } else {
            $this->strict_mode = true;
        }

        if (is_string($methods))
            $methods = [$methods];

        if (!is_array($methods))
            throw new InvalidArgumentException("Allowed methods must be a string or an array of methods", 1);

        foreach(self::$methods as $method) {
            if (in_array($method, $methods)) {
                if (!$this->has($method))
                    $this->set($method, []);
            } else {
                $this->remove($method);
            }
        }
    }

    public function addRoute($methods, $patern, $handler)
    {
        if (is_string($methods))
            $methods = [$methods];

        foreach ($methods as $method) {
            if ('ANY' == strtoupper($method)) {
                foreach($this->toArray() as $am=>$array) {
                    if (is_array($array))
                        $this->addRoute($am, $patern, $handler);
                }
            } else {
                # todo -- find some sane way of calling
                $method = Request::validate('method', $method);

                $def = $this->get($method);
                if (!is_array($def) && $this->strict_mode)
                    throw new InvalidArgumentException('Not an allowed method: ' . $method);

                $def[] = [
                    'patern'  => $patern,
                    'handler' => $handler,
                ];
                $this->set($method, $def);
            }
        }
    }

    public function process($method, $path='/')
    {
        # todo -- use a sane way to call
        $method = Request::validate('method', $method);

        $path = ltrim(rtrim($path, '/'), '/') ?: '/';
        $found = false;
        $routes = $this->get($method, []);

        foreach($routes as $route) {
            extract($route);
            preg_match('|(' . $patern . ')|', $path, $m);
            $matches_regex = (isset($m[1]) && $m[1] == $path) ? true : false;

            if (!$matches_regex)
                continue;

            ### If its a callback function
            if (is_callable($handler)) {
                # $response = $callable($this->request, $this->response);
                return ([
                    'type'    => 'closure',
                    'handler' => $handler
                ]);
            } elseif (is_string($handler)) {
                # method@Controller
                return ([
                    'type'    => 'string',
                    'handler' => $handler
                ]);
            } else {
                throw new InvalidArgumentException('Handler must be a closure or a string');
            }
        }
        return ([
            'type'    => null,
            'handler' => null
        ]);
    }
}
