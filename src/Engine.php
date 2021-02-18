<?php

/**
 * IrfanTOOR\Engine
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR;

use Exception;
use IrfanTOOR\Collection;
use IrfanTOOR\Debug;
use IrfanTOOR\Engine\{
    ExceptionHandler,
    ShutdownHandler
};

use IrfanTOOR\Container;

use Psr\Http\Message\{
    RequestInterface,
    ResponseInterface,
    ServerRequestInterface,
};

/**
 * Irfan's Engine -- can be used to receive the ServerRequest, assign a
 * RequestHandler to handle it and send the generated Response. It can
 * handle the basic configuration, Exceptions and unexpected Shutdowns etc.
 */
class Engine
{
    const NAME        = "Irfan's Engine";
    const DESCRIPTION = "A bare-minimum PHP framework";
    const VERSION     = "4.0.1";

    /**
     * Status values, which indicate the possible state of the engine at a given
     * point in time
     */
    const STATUS_OK          =  1;
    const STATUS_TRANSIT     =  0;
    const STATUS_EXCEPTION   = -1;
    const STATUS_ERROR       = -2;
    const STATUS_FATAL_ERROR = -3;

    /** @var Container */
    protected $container;

    /** @var int -- status of the engine */
    protected static $status;

    /** @var  Collection -- The configuration collection */
    protected $config;

    /** @var  string -- Http provider */
    protected $provider;

    /** @var callback -- Request handler callback */
    protected $handler;

    /*
     * Irfan's Engine constructor
     *
     * @param array $init Array to initialize the engine
     */
    function __construct($init = [])
    {
        ob_start();
        self::$status = $init['status'] ?? self::STATUS_TRANSIT;

        # shutdown handler
        register_shutdown_function(
            function () {
                $contents = ob_get_clean();
                // Debug::enable(0);

                if (self::STATUS_OK === self::$status) {
                    echo $contents;

                    $error = error_get_last();
                    throw new Exception($error['message']);
                    if ($error)
                        print_r($error);

                    return;
                }

                $response =
                    (new ShutdownHandler(self::NAME, self::VERSION, self::$status))
                    ->handle($contents)
                ;

                $this->send($response);
            }
        );

        $init = is_array($init) ? $init : [];

        # Initial debug while warm up
        $dl = $init['debug']['level'] ?? 0;
        $this->enableDebug($dl);

        # Load the config from the file
        $file = $init['config_file'] ?? null;

        if ($file && is_string($file)) {
            if (!is_file($file))
                throw new \RuntimeException("Config file: $file, does not exist.");

            $config_from_file = require $file;

            if (!is_array($config_from_file))
                throw new \RuntimeException(
                    "Config file: $file, does not return an array."
                );

            $init = array_merge($init, $config_from_file);
            unset($init['config_file']);
        }

        # Config
        $this->config = new Collection($init);
        $this->config->lock();

        # Readjust the debug level if the need be
        $config_dl = $this->config('debug.level', 0);
        if ($config_dl !== $dl)
            $this->enableDebug($config_dl);

        # Init Container
        $this->container = new Container();
        $this->container->addExtension('di', new \DI\Container());

        # Select the default Http povider
        $this->provider =
            rtrim($this->config('http.provider', 'IrfanTOOR\\Http'), "\\")
            . "\\"
        ;

        # Default timezone Europ/Paris (+1)
        date_default_timezone_set(
            $this->config('admin.timezone', 'Europe/Paris')
        );
    }

    /**
     * Returns the config element
     *
     * @param string $key     Key of the config item
     * @param mixed  $default Default value associated with the config key
     *
     * @return mixed
     */
    public function config(string $key, $default = null)
    {
        return $this->config->get($key, $default);
    }

    /**
     * Intercept the calls to create, createFromEnvironment/createFromGlobals
     */
    public function __call($method, $args = [])
    {
        $class = $args[0];
        $args  = $args[1] ?? [];

        switch ($method) {
            case 'createFromEnvironment':
            case 'createFromGlobals':
                $fclass = $this->config(
                    'provider.mappings.' . $class,
                    $this->provider . $class . 'Factory'
                );

                try {
                    $factory = $this->container->make($fclass, $args);
                } catch (\Throwable $e) {
                    $fclass = $this->provider . 'Factory\\' . $class . "Factory";
                    $factory = $this->container->make($fclass, $args);
                }

                if (method_exists($factory, 'createFromEnvironment'))
                    return call_user_func_array(
                        [$factory, 'createFromEnvironment'],
                        $args
                    );
                elseif(method_exists($factory, 'createFromGlobals'))
                    return call_user_func_array(
                        [$factory, 'createFromGlobals'],
                        $args
                    );

            case 'create':
                $class = $this->config(
                    'provider.mappings.' . $class,
                    $this->provider . $class
                );

                return $this->container->make($class, $args);
        }
    }

    /**
     * Enables the debuging
     *
     * @param int $level Debug level, can be from 0 to 3, default value is 1
     */
    public function enableDebug(int $level = 1)
    {
        Debug::enable($level);

        # Use this Exception handler instead of Debug's
        set_exception_handler(
            function ($e) {
                self::$status = self::STATUS_EXCEPTION;
                $handler = new ExceptionHandler();
                $handler->handle($e);
            }
        );
    }

    /**
     * Adds a request handler to engine
     *
     * @param callback|RequestHandler $handler
     */
    public function addHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * Handles the given Request
     *
     * @param ServerRequest $request
     * @return Response
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = null;

        if (!$this->handler)
            throw new Exception("No handler defined", 1);

        $response =
            method_exists($this->handler, 'handle')
            ? $this->handler->handle($request)
            : $this->handler->__invoke($request)
        ;

        if (!$response || !is_a($response, ResponseInterface::class)) {
            throw new Exception('Response not returned by the handler');
        }

        return $response->withHeader("Engine", self::NAME . " v" . self::VERSION);
    }

    /**
     * Runs the engine, the processes the request
     */
    public function run()
    {
        $request = $this->createFromEnvironment('ServerRequest');
        $response = $this->handle($request);
        $this->send($response);
    }

    /**
     * Send the response to the client
     *
     * @param ResponseInterface $response
     */
    public function send(ResponseInterface $response)
    {
        if (!headers_sent()) {
            # Send the status header
            $status = $response->getStatusCode();
            $http_line = sprintf('HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            );

            # Send the other headers
            foreach ($response->getHeaders() as $k => $v)
                header($k . ":" . $response->getHeaderLine($k));
        }

        # Send the body of the response
        $stream = $response->getBody();

        if ($stream->isSeekable())
            $stream->rewind();

        while (!$stream->eof()) {
            echo $stream->read(8192);
        }

        $stream->close();

        # We have arrived at the end, so mark the status as OK to avoid
        # the ShutdownHandler's processing
        self::$status = self::STATUS_OK;
    }
}
