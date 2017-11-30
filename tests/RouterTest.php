<?php

use IrfanTOOR\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    function getRouter($allowed = null)
    {
        return new Router($allowed);
    }

    function testRouterInstance()
    {
        $r = $this->getRouter();
        $this->assertInstanceOf('IrfanTOOR\Router', $r);
        $this->assertInstanceOf('IrfanTOOR\Collection', $r);
    }

    function testAllowedMethods()
    {
        $r = $this->getRouter(['GET']);
        $expected =  [
            'CONNECT' => 0,
            'DELETE' => 0,
            'GET' => [],
            'HEAD' => 0,
            'OPTIONS' => 0,
            'PATCH' => 0,
            'POST' => 0,
            'PUT' => 0,
            'TRACE' => 0,
        ];

        $this->assertEquals($expected, $r->toArray());

        $r->setAllowedMethods(['GET', 'POST']);
        $expected =  [
            'CONNECT' => 0,
            'DELETE' => 0,
            'GET' => [],
            'HEAD' => 0,
            'OPTIONS' => 0,
            'PATCH' => 0,
            'POST' => [],
            'PUT' => 0,
            'TRACE' => 0,
        ];
        $this->assertEquals($expected, $r->toArray());

        # All are allowed
        $r->setAllowedMethods();
        $expected =  [
            'CONNECT' => [],
            'DELETE' => [],
            'GET' => [],
            'HEAD' => [],
            'OPTIONS' => [],
            'PATCH' => [],
            'POST' => [],
            'PUT' => [],
            'TRACE' => [],
        ];
        $this->assertEquals($expected, $r->toArray());

        try {
            $r = new Router(1);
        }
        catch(Exception $e) {
        }
        $this->assertInstanceOf('IrfanTOOR\Exception', $e);
        $this->assertEquals("Allowed methods is a string or an array of methods", $e->getMessage());
    }

    function testAddRoute()
    {
        $r = $this->getRouter(['GET']);

        $closure = function(){};

        # Allowed method
        $r->add('GET', '/', 'hello@MyClass');
        $r->add('GET', '.*', $closure);
        $this->assertEquals(['patern'=>'/', 'callable'=>'hello@MyClass'], $r['GET'][0]);
        $this->assertEquals(['patern'=>'.*', 'callable'=>$closure], $r['GET'][1]);

        # Unknown method
        try {
            $r->add('UNKNOWN', '.*', 'hello@MyClass');
        } catch (Exception $e) {
        }
        $this->assertInstanceOf('IrfanTOOR\Exception', $e);
        $this->assertEquals("Method: 'UNKNOWN', is not defined", $e->getMessage());

        # Not allowed method
        try {
            $r->add('POST', '.*', 'hello@MyClass');
        } catch (Exception $e) {
        }
        $this->assertInstanceOf('IrfanTOOR\Exception', $e);
        $this->assertEquals("Method: 'POST', is not allowed", $e->getMessage());
    }

    function testProcess()
    {
        $r = $this->getRouter(['GET']);
        $closure = function(){
            return 'Hello World!';
        };

        $r->add('GET', '/', 'hello@MyClass');
        $r->add('GET', '.*', $closure);
        $expected = [
            'found' => true,
            'type' => 'string',
            'callable' => 'hello@MyClass',
        ];

        $route = $r->process('GET', '');
        $this->assertEquals($expected, $route);

        $route = $r->process('GET', '/');
        $this->assertEquals($expected, $route);

        $expected['type'] = 'closure';
        $expected['callable'] = $closure;

        $route = $r->process('GET', '/hello');
        $this->assertEquals($expected, $route);

        $route = $r->process('GET', '/hello/world');
        $this->assertEquals($expected, $route);
    }
}
