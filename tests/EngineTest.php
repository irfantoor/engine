<?php

use IrfanTOOR\Debug;
use IrfanTOOR\Engine;
use IrfanTOOR\Engine\{RequestHandlerInterface, ShutdownHandler};
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
use IrfanTOOR\Test;
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
        $this->assertImplements(RequestHandlerInterface::class, $ie);

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

    function testShutdownHandler()
    {
        $ie = $this->getEngine(['debug' => ['level' => 1]]);
        $sh = new ShutdownHandler($ie);
        $this->assertInstanceOf(ShutdownHandler::class, $sh);
        $this->assertImplements(RequestHandlerInterface::class, $sh);

        $request = $ie->createFromGlobals('ServerRequest');

        $response = $sh->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $body = $response->getBody()->__toString();
        $this->assertNotFalse(strpos($body, 'Error'));
        $this->assertNotFalse(strpos($body, 'Nothing to display ...'));
        $this->assertNotFalse(strpos($body, 'Increase the debug level to view the details'));
        $this->assertNotFalse(strpos($body, $ie::NAME . ' v' . $ie::VERSION));

        $request = $request->withAttribute('contents', 'its a test!');
        $response = $sh->handle($request);
        $body = $response->getBody()->__toString();
        $this->assertFalse(strpos($body, 'Nothing to display ...'));
        $this->assertNotFalse(strpos($body, 'its a test!'));

        # title according to the status
        $list = [
            $ie::STATUS_OK          => 'Shutdown Handler',
            $ie::STATUS_TRANSIT     => 'Shutdown Handler',
            $ie::STATUS_EXCEPTION   => 'Exception',
            $ie::STATUS_ERROR       => 'Error',
            $ie::STATUS_FATAL_ERROR => 'Error',
        ];

        foreach ($list as $status => $title) {
            $request = $request->withAttribute('status', $status);
            $response = $sh->handle($request);
            $body = $response->getBody()->__toString();
            $this->assertNotFalse(strpos($body, $title));
        }

        # without passing the Engine Instance
        $sh = new ShutdownHandler();
        $response = $sh->handle($request);
        $body = $response->getBody()->__toString();
        $this->assertNotFalse(strpos($body, $ie::NAME . ' v' . $ie::VERSION));
    }

    function testCreate()
    {
        $ie = $this->getEngine();

        $this->assertInstanceOf( 'IrfanTOOR\\Http\\Request', $ie->create( 'Request', ['GET', 'http://example.com/'] ) );
        $this->assertInstanceOf( 'IrfanTOOR\\Http\\Response', $ie->create( 'Response' ) );
        $stream = $ie->create( 'Stream', [file_get_contents(__FILE__)] );
        $this->assertInstanceOf( 'IrfanTOOR\\Http\\Stream', $stream );
        $this->assertInstanceOf( 'IrfanTOOR\\Http\\UploadedFile', $ie->create( 'UploadedFile', [$stream] ) );
        $this->assertInstanceOf( 'IrfanTOOR\\Http\\Uri',     $ie->create( 'Uri' ) );
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
}
