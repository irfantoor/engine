<?php

use IrfanTOOR\Test;

use IrfanTOOR\Engine;
use IrfanTOOR\Debug;
use IrfanTOOR\Http\{
    Cookie,
    Environment,
    Request,
    Response,
    ServerRequest,
    Stream,
    UploadedFile,
    Uri
};
use Psr\Http\Message\{
    MessageInterface,
    RequestInterface,
    ResponseInterface,
    ServerRequestInterface,
    UploadedFileInterface,
    UriInterface
};

class EngineTest extends Test
{
    function getEngine($init = [])
    {
        # If we wont set it, it might make the this class die silently!
        if (!isset($init['status']))
            $init['status'] = Engine::STATUS_OK;

        if (!isset($init['debug']))
            $init['debug'] = ['level' => 2];

        return new MockEngine($init);
    }

    function testInstance()
    {
        $ie = $this->getEngine();

        $this->assertInstanceOf(Engine::class, $ie);
        $this->assertString(Engine::NAME);
        $this->assertString(Engine::DESCRIPTION);
        $this->assertString(Engine::VERSION);
    }

    function testDefaults()
    {
        $ie = new MockEngine();
        $this->assertEquals([], $ie->get('config')->toArray());

        $ie = $this->getEngine();
        $this->assertEquals('IrfanTOOR\\Http\\', $ie->get('provider'));
    }

    function testInit()
    {
        $config = [
            'status' => Engine::STATUS_OK,
            'debug' => [
                'level' => 1,
            ],
            'hello' => 'world',
        ];

        $ie = $this->getEngine($config);

        foreach ($config as $k => $v) {
            $this->assertEquals($v, $ie->config($k));
        }
    }

    function testInitConfigFile()
    {
        $config = require("folder/config.php");
        $ie = $this->getEngine([
            'config_file' => __DIR__ . "/folder/config.php"
        ]);

        foreach ($config as $k => $v) {
            $this->assertEquals($v, $ie->config($k));
        }
    }

    function testExceptionHandler()
    {
        # todo -- verify that the exceptions are handled
        # all the raised exceptions are handled by Debug
    }

    function testErrorHandler()
    {
        # todo -- verify that the errors are handled
        # all the raised errors are handled by Debug
    }

    function testShutdownHandler()
    {
        # todo -- verify that the unexpected shutdown is handled
    }

    function testCreate()
    {
        $ie = $this->getEngine();

        $classes = [
            'Request',
            'Response',
            'ServerRequest',
            'UploadedFile',
            'Uri'
        ];

        foreach ($classes as $classname) {
            $class = $ie->create($classname);
            $classname = 'IrfanTOOR\\Http\\' . $classname;
            $this->assertInstanceOf($classname, $class);
        }
    }

    function testCreateFromEnvironment()
    {
        $ie = $this->getEngine();

        $classes = [
            'ServerRequest',
            'Uri'
        ];

        foreach ($classes as $classname) {
            $class = $ie->createFromEnvironment($classname);
            $classname = 'IrfanTOOR\\Http\\' . $classname;
            $this->assertInstanceOf($classname, $class);
        }
    }

    function testAddHandler()
    {
        $ie = $this->getEngine();
        $response = $ie->create('Response');

        $ie->addHandler(function($request) use($response){
            $this->assertInstanceOf(Request::class, $request);
            $response->getBody()->write('hello world by handler');
            return $response;
        });

        $ie->run();
        $this->assertInstanceOf(Response::class, $ie->get('response'));
        $this->assertEquals("hello world by handler", (string) $ie->get('contents'));
    }

    /**
     * throws: Exception::class
     * message: No handler defined
     */
    function testNoHandlerDefined()
    {
        $ie = $this->getEngine();
        $ie->run();
    }

    /**
     * throws: Exception::class
     * message: Response not returned by the handler
     */
    function testHandlerNotReturningRequest()
    {
        $ie = $this->getEngine();
        $ie->addHandler(function ($request) {
            return null;
        });

        $ie->run();
    }

    function testRunandSend()
    {
        $ie = $this->getEngine();
        $response = $ie->create('Response');

        $this->assertMethod($ie, 'run');
        $this->assertMethod($ie, 'send');

        # send/handler is not called yet
        $this->assertNull($ie->get('response'));

        $ie->send($response);
        $r = $ie->get('response');

        # send was called
        $this->assertNotNull($r);
        $this->assertInstanceOf(Response::class, $r);

        # handler was not called
        $this->assertEquals("", (string) $ie->get('contents'));

        $ie = $this->getEngine();
        $response = $ie->create('Response');

        $ie->addHandler(function($request) use($response){
            $this->assertInstanceOf(Request::class, $request);
            $response->getBody()->write('hello world by handler');
            return $response;
        });

        $ie->run();
        $response = $ie->get('response');

        # send was be called
        $this->assertNotNull($response);
        $this->assertInstanceOf(Response::class, $response);

        # handler was called
        $this->assertEquals("hello world by handler", (string) $ie->get('contents'));
        $body = $response->getBody();
        $this->assertInstanceOf(Stream::class, $body);

        # resource is closed
        $this->assertFalse($body->isReadable());
    }
}

class MockEngine extends Engine
{
    protected $response = null;
    protected $contents = null;

    public function get($v)
    {
        return $this->$v;
    }

    public function send(ResponseInterface $response)
    {
        $this->response = $response;
        ob_start();
        parent::send($response);
        $this->contents = ob_get_clean();
    }

    public function shutdown()
    {
        exit;
    }
}
