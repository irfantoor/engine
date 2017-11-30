<?php

use IrfanTOOR\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    function testCollectionInstance()
    {
        $e = new Environment();
        $this->assertInstanceOf('IrfanTOOR\Environment', $e);
        $this->assertInstanceOf('IrfanTOOR\Collection', $e);
    }

    function testServerParameters()
    {
        $e = Environment::getInstance();
        $env = array_merge($_SERVER, ['session'=>$_SESSION]);

        $this->assertEquals($env, $e->toArray());
    }

    function testMockingEnv()
    {
        $mock = [
            'REQUEST_TIME' => 0,
            'Hello'        => 'World!',
        ];

        $e = new Environment($mock);

        $env = array_merge($_SERVER, $mock, ['session'=>$_SESSION]);

        # Mocked Environment variables are added/modified
        $this->assertEquals(0, $e['REQUEST_TIME']);
        $this->assertEquals('World!', $e['Hello']);

        $this->assertEquals($env, $e->toArray());

        # Environment is locked
        $e->remove('Hello');
        $this->assertTrue($e->has('Hello'));
        $this->assertEquals('World!', $e['Hello']);
    }

    function testGetInstance()
    {
        $e = Environment::getInstance();
        $e2 = Environment::getInstance();
        $this->assertEquals($e, $e2);
        $this->assertSame($e, $e2);
    }
}
