<?php

namespace IrfanTOOR;

use Exception;
use IrfanTOOR\Collection;
use IrfanTOOR\Container;
use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Constants;

class Engine
{
    const NAME        = "Irfan's Engine";
    const DESCRIPTION = "A bare-minimum PHP framework";
    const VERSION     = "2.1";

    protected $config;
    protected $classes;
    protected $container;

    /*
     * Constructs the Irfan's Engine
     *
     */
    function __construct($config=[])
    {
        # preprocess config
        # default env variables to be merged with Server Request Environment
        if (isset($config['default']['Environment'])) {
            $sre = isset($config['default']['ServerRequest']['env'])
                        ? $config['default']['ServerRequest']['env']
                        : [];
            $sre = array_merge($config['default']['Environment'], $sre);
            $config['default']['ServerRequest']['env'] = $sre;
        }

        $this->config    = new Collection($config);
        $this->container = new Container();

        # Default classes can be overriden by $config['default']['classes']
        # e.g. $config['default']['classes']['Environmet'] = 'My\\Environment';
        $defaults = [
            # default factories
            'Cookie'        => 'IrfanTOOR\\Engine\\Http\\Cookie',
            'UploadedFile'  => 'IrfanTOOR\\Engine\\Http\\UploadedFile',

            # default classes
            'Environment'   => 'IrfanTOOR\\Engine\\Http\\Environment',
            'Request'       => 'IrfanTOOR\\Engine\\Http\\Request',
            'Response'      => 'IrfanTOOR\\Engine\\Http\\Response',
            'ServerRequest' => 'IrfanTOOR\\Engine\\Http\\ServerRequest',
            'Uri'           => 'IrfanTOOR\\Engine\\Http\\Uri',
        ];

        $this->classes = $this->config('default.classes', []);

        foreach ($defaults as $k => $v) {
            if (!isset($this->classes[$k])) {
                $this->classes[$k] = $v;
            }
        }

        # Factory functions for Cookie and Uploaded file
        foreach (
            [
                'UploadedFile',
                'Cookie'
            ] as $name
        ) {
            $cname = $this->classname($name);
            $this->container->factory($name, function($args = []) use($cname) {
                return new $cname($args);
            });
        }

        # Initialize other default class instances
        foreach (
            [
                # 'UploadedFile',
                'Environment',
                'Request',
                'Response',
                'ServerRequest',
                'Uri',
            ] as $name
        ) {
            $cname = $this->classname($name);
            $defaults = $this->config('default.' . $name, []);
            $this->container->set($name, function() use($cname, $defaults) {
                return new $cname($defaults);
            });
        }

        # Set default timezone
        date_default_timezone_set($this->config("timezone", "Europe/Paris"));
        
        # Sets the debug level of engine
        $dl = $this->config('debug.level', 0);
        if ($dl) {
            Debug::enable($dl);
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
        if (strpos($method, 'get') === 0) {
            $m = substr($method, 3);
            if ($this->container->has($m)) {
                $obj = $this->container[$m];
                if (is_callable($obj)) {
                    return call_user_func_array($obj, $args);
                } else {
                    return $obj;
                }
            }
        }

        throw new Exception("Unknown method: '$method'");
    }

    /**
     * Returns the Version
     *
     * @return string version
     */
    public function getVersion()
    {
        return self::VERSION;
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
     * Returns the default or configured classname with namespace
     *
     * @param string $id
     *
     * @return string
     */
    public function classname($id)
    {
        # todo -- returns null class instead of null
        return isset($this->classes[$id]) ? $this->classes[$id] : null;
    }
    
    /**
     * Runs the engine, the processes the request
     *
     */
    function run()
    {
        $request  = $this->getServerRequest();
        $response = $this->getResponse();
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
        # throw new Exception('function: "process", does not exist in the derived class');
        return $response;
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
