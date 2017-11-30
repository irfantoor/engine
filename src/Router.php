<?php

namespace IrfanTOOR;

use IrfanTOOR\Collection;
use IrfanTOOR\Debug;
use IrfanTOOR\Exception;

class Router extends Collection
{
    function __construct($allowed_methods = null)
    {
        parent::__construct();
        $this->setAllowedMethods($allowed_methods);
    }

    function setAllowedMethods($allowed_methods=null)
    {
        # Reset all methods
        $methods = explode('|', "CONNECT|DELETE|GET|HEAD|OPTIONS|PATCH|POST|PUT|TRACE");
        foreach($methods as $method)
            $this->set($method, 0);

        $allowed_methods = $allowed_methods ?: $methods;
        if (is_string($allowed_methods))
            $allowed_methods = [$allowed_methods];

        if (!is_array($allowed_methods))
            throw new Exception("Allowed methods is a string or an array of methods", 1);

        foreach($allowed_methods as $method) {
            $method = strtoupper($method);

            if ($this->has($method))
                $this->set($method, []);
        }
    }

    function add($methods, $patern, $callable) {
        if (is_string($methods))
            $methods = [$methods];

        foreach($methods as $method) {
            if ($method=='ANY') {
                foreach($this->toArray() as $am=>$array) {
                    if (is_array($array))
                        $this->add($am, $patern, $callable);
                }
            }
            else {
                if (!$this->has($method))
                    throw new Exception("Method: '$method', is not defined", 1);

                $routes = $this->get($method);
                if ($routes === 0)
                    throw new Exception("Method: '$method', is not allowed", 1);

                $routes[] = [
                    'patern'	=> $patern,
                    'callable'	=> $callable
                ];

                $this->set($method, $routes);
            }
        }
    }

    function process($method, $uri)
    {
        $uri = parse_url($uri);
        $base_path = ltrim(rtrim($uri['path'], '/'), '/') ?: '/';
        $found = false;

        $routes = $this->get($method, []);

        foreach($routes as $route) {
            extract($route);
            preg_match('|(' . $patern . ')|', $base_path, $m);
            $matches_regex = (isset($m[1]) && $m[1] == $base_path) ? true : false;
            if (!$matches_regex)
                continue;

            $found = true;
            $response = $contents = null;

            ### If its a callback function
            if (is_callable($callable)) {
                # $response = $callable($this->request, $this->response);
                return ([
                    'found'    => true,
                    'type'     => 'closure',
                    'callable' => $callable
                ]);
            }

            ### If its a method@Controller
            elseif (is_string($callable)) {
                return ([
                    'found'    => true,
                    'type'     => 'string',
                    'callable' => $callable
                ]);
            }

            else {
                throw new Exception("Callable must be a closure or a string", 1);
            }
        }

        if (!$found){
            return ([
                'found'    => false,
                'type'     => null,
                'callable' => null
            ]);

            #($err = error_get_last()) && Debug::dump($err);
            $this->response['status'] = 404;
            $this->response->send();
        }
    }

    function dump()
    {
        $list = [];
        echo '<pre>';
        foreach($this->toArray() as $k=>$v)
            if ($v) {
                foreach($v as $item)
                    $list[] = [$k, $item['patern'], $item['callable']];
            }

        Debug::table($list, ['Method', 'Patern', 'Callable'], 'Routes Dump');
    }
}
