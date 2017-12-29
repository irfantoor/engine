<?php

use IrfanTOOR\Engine\Router;
use IrfanTOOR\Engine\Http\RequestMethod;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    function getRouter($allowed = null)
    {
        $router = new Router();
        if ($allowed)
            $router->setAllowedMethods($allowed);

        return $router;
    }

    function testRouterInstance()
    {
        $router = $this->getRouter();
        $this->assertInstanceOf(IrfanTOOR\Engine\Router::class, $router);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $router);
    }

    function testAllowedMethods()
    {
        $router = $this->getRouter();
        $expected =  [
            'CONNECT' => [],
            'DELETE' => [],
            'PURGE' => [],
            'GET' => [],
            'HEAD' => [],
            'OPTIONS' => [],
            'PATCH' => [],
            'POST' => [],
            'PUT' => [],
            'TRACE' => [],
        ];

        $this->assertEquals($expected, $router->toArray());

        $router->setAllowedMethods(['GET', 'POST']);
        $this->assertEquals(['GET', 'POST'], $router->keys());

        # All are allowed
        $router->setAllowedMethods();
        $expected =  [
            'CONNECT' => [],
            'DELETE' => [],
            'PURGE' => [],
            'GET' => [],
            'HEAD' => [],
            'OPTIONS' => [],
            'PATCH' => [],
            'POST' => [],
            'PUT' => [],
            'TRACE' => [],
        ];
        $this->assertEquals($expected, $router->toArray());
    }

    function testAddRoute()
    {
        $router = $this->getRouter(['GET']);

        $closure = function(){};

        # Allowed method
        $router->addRoute('GET', '/', 'hello@MyClass');
        $router->addRoute('GET', '.*', $closure);
        $this->assertEquals(['patern'=>'/', 'handler'=>'hello@MyClass'], $router['GET'][0]);
        $this->assertEquals(['patern'=>'.*', 'handler'=>$closure], $router['GET'][1]);

        # Not allowed method
        try {
            $router->addRoute('POST', '.*', 'post@MyClass');
        } catch (Exception $e) {

        }
        $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        $this->assertEquals('Not an allowed method: POST', $e->getMessage());
    }

    function testProcess()
    {
        $router = $this->getRouter(['GET']);
        $closure = function(){
            return 'Hello World!';
        };

        $router->addRoute('GET', '/', 'hello@MyClass');
        $router->addRoute('GET', '.*', $closure);
        $expected = [
            'type' => 'string',
            'handler' => 'hello@MyClass',
        ];

        $route = $router->process('GET', '');
        $this->assertEquals($expected, $route);

        $route = $router->process('GET', '/');
        $this->assertEquals($expected, $route);

        $expected['type'] = 'closure';
        $expected['handler'] = $closure;

        $route = $router->process('GET', '/hello');
        $this->assertEquals($expected, $route);

        $route = $router->process('GET', '/hello/world');
        $this->assertEquals($expected, $route);
    }
}
