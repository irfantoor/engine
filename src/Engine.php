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

use Throwable;

/**
 * Irfan's Engine -- can be used to receive the ServerRequest, assign a
 * RequestHandler to handle it and send the generated Response. It can
 * handle the basic configuration, Exceptions and unexpected Shutdowns etc.
 */
class Engine
{
    const NAME        = "Irfan's Engine";
    const DESCRIPTION = "A bare-minimum PHP framework";
    const VERSION     = "4.0.6";

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

    /**
     * Irfan's Engine constructor
     *
     * @param array $init Array to initialize the engine
     */
    function __construct(array $init = [])
    {
        self::$status = $init['status'] ?? self::STATUS_TRANSIT;
        register_shutdown_function([$this, "shutdown"]);

        # warm up debug
        $dl = $init['debug']['level'] ?? 0;
        Debug::enable($dl);

        # exception / error handlers
        set_exception_handler(function($e) {
            self::$status = self::STATUS_EXCEPTION;
            Debug::exceptionHandler($e);
            exit;
        });

        set_error_handler(function($type, $message, $file, $line) {
            self::$status = self::STATUS_ERROR;
            Debug::errorHandler($type, $message, $file, $line);
            exit;
        });

        # Init Container
        $this->container = new Container();
        $this->container->addExtension('di', new \DI\Container());

        # Select the default Http povider
        $this->provider = "IrfanTOOR\\Http\\";

        ob_start();
        $this->config = new Collection();
        $this->loadConfig($init);
    }

    /**
     * Loads the configuration
     *
     * @param array $init Associative array of key to value
     */
    public function loadConfig(array $init = [])
    {
        # Load the config from the file
        $file = $init['config_file'] ?? null;

        if ($file) {
            $config_from_file = include $file;

            if (!is_array($config_from_file))
                throw new \RuntimeException(
                    "Config file: $file, does not return an array."
                );

            $init = array_merge($init, $config_from_file);
        }

        # Config
        $this->config->setMultiple($init);
        $this->config->remove('config_file');
        $this->config->lock();

        $this->provider = 
            rtrim(
                $this->config('http.provider') ?? $this->provider,
                "\\"
            ) . "\\"
        ;

        # Readjust the debug level if the need be
        $dl = $this->config('debug.level', 0);
        if (Debug::getLevel() !== $dl)
            Debug::enable($dl);

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
                    'http.mappings.' . $class,
                    $this->provider . $class . 'Factory'
                );

                try {
                    $factory = $this->container->make($fclass, $args);
                } catch (Throwable $th) {
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
                    'http.mappings.' . $class,
                    $this->provider . $class
                );

                return $this->container->make($class, $args);
        }
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

        if (!$response || !is_a($response, ResponseInterface::class))
            throw new Exception('Response not returned by the handler');

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

            header($http_line);

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

    /**
     * Called at shutdown
     */
    public function shutdown()
    {
        # if the request has already been sent
        if (self::STATUS_OK === self::$status)
            return;

        # to catch any errors comming from unexpected shutdown of Engine
        # and could not be caught earlier
        while ($e = error_get_last()) {
            self::$status = self::STATUS_ERROR;
            Debug::errorHandler($e['type'], $e['message'], $e['file'], $e['line']);
            error_clear_last();
        }

        $contents = ob_get_clean();

        $response =
            (new ShutdownHandler($this))
            ->handle($contents, self::$status)
        ;

        $this->send($response);
    }
}
