<?php

use IrfanTOOR\Engine;
use IrfanTOOR\Engine\Http\Response;

use PHPUnit\Framework\TestCase;

class MockResponse extends Response
{
}

class MockEngine extends Engine
{
    protected $result;

    function process($request, $response, $args)
    {   
        $response->write('Hello World!');
        $response = $response->withHeader('Engine', 'IE v9');

        return $response;
    }

    function finalize($request, $response, $args)
    {
        $response = $response->withHeader('finalize', 'processed');
        $this->result = [$request, $response, $args];

        # response->send();
    }

    function getResult()
    {
        return $this->result;
    }
}

class EngineTest extends TestCase
{
    protected $ie;

    protected function setUp($config = [])
    {
        $this->ie = new MockEngine ($config);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(IrfanTOOR\Engine::class, $this->ie);
    }

    public function testConfig()
    {
        $config = [
            'debug' => [
                'level' => 0,
            ],

            'domain' => [
                'name' => 'Example',
                'site' => 'example.com',
            ],
        ];

        $ie = new MockEngine($config);

        $this->assertNotNull($ie->config('debug.level'));
        $this->assertEquals($config['debug']['level'], $ie->config('debug.level'));
        $this->assertEquals($config['domain']['name'], $ie->config('domain.name'));
        $this->assertEquals($config['domain']['site'], $ie->config('domain.site'));
        $this->assertNull($ie->config('domain.site.host'));
        $this->assertEquals('github', $ie->config('domain.site.host', 'github'));
    }


    public function testDefaultEngineHttpClasses()
    {
        $ie = new MockEngine();
        $ie->run();

        // $result = $ie->getResult(IrfanTOOR\Engine\Http\Response);

        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Response::class, $ie->Response());
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Request::class, $ie->Request());
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Cookie::class, $ie->Cookie());
        $this->assertNotInstanceOf(MockResponse::class, $ie->Response());
    }

    public function testLoadProvidedDefaultClasses()
    {
        $config = [
            'default' => [
                'classes' => [
                    'Response' => 'MockResponse',
                ],
                
                'Environment' => [
                    'hello' => 'world',
                    'Hello' => 'World'
                ],

                'ServerRequest' => [
                    'env' => [
                        'hello'   => 'world',
                        'Hello'   => 'World!',
                        'Missing' => 'Not'
                    ],
                ],

            ],
        ];

        $ie = new MockEngine($config);
        $ie->run();
        $this->assertInstanceOf(MockResponse::class, $ie->Response());
        
        # Environment contains the configured env variables
        $req = $ie->ServerRequest();
        $this->assertEquals('world', $req->getAttribute('env.hello'));
        $this->assertEquals('World!', $req->getAttribute('env.Hello'));
        $this->assertEquals('Not', $req->getAttribute('env.Missing'));

        # Cookie returns a new instance
        $c1 = $ie->Cookie(['name' => 'hello', 'value' => 'world']);
        $c2 = $ie->Cookie(['name' => 'hello', 'value' => 'world']);

        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Cookie::class, $c1);
        $this->assertEquals($c1, $c2);
        $this->assertNotSame($c1, $c2);

        # Uploaded file returns a new instance
        $f1 = $ie->UploadedFile('hello.txt', 'world.txt', 'text/plain');
        $f2 = $ie->UploadedFile('hello.txt', 'world.txt', 'text/plain');

        $this->assertInstanceOf(IrfanTOOR\Engine\Http\UploadedFile::class, $f1);
        $this->assertEquals($f1, $f2);
        $this->assertNotSame($f1, $f2);
    }

    public function testRun()
    {
        $result = $this->ie->getResult();

        $this->assertNull($result);

        $this->ie->run();

        $result = $this->ie->getResult();

        $this->assertInstanceOf(Psr\Http\Message\RequestInterface::class, $result[0]);
        $this->assertInstanceOf(Psr\Http\Message\ResponseInterface::class, $result[1]);
        $this->assertTrue(is_array($result[2]));
    }

    public function testProcess()
    {
        $this->ie->run();
        $result = $this->ie->getResult();
        $res = $result[1];

        # assert the actions in the process phase
        $this->assertEquals('Hello World!', $res->getBody());
        $this->assertEquals('Engine: IE v9', $res->getHeaderLine('engine'));
    }


    public function testFinalize()
    {
        $this->ie->run();

        $result = $this->ie->getResult();
        $res = $result[1];

        $this->assertEquals('finalize: processed', $res->getHeaderLine('finalize'));
    }
}
