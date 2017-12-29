<?php

use IrfanTOOR\Engine\Http\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    function testCollectionInstance()
    {
        $e = new Environment();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Environment::class, $e);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $e);
    }

    function testServerParameters()
    {
        $env = new Environment();

        foreach($_SERVER as $k=>$v) {
            $this->assertEquals($v, $env->get($k));
        }
    }

    function testMockingEnv()
    {
        $mock = [
            'REQUEST_TIME' => 0,
            'Hello'        => 'World!',
        ];

        $env = new Environment($mock);

        $menv = array_merge($_SERVER, $mock);

        # Mocked Environment variables are added/modified
        foreach($menv as $k=>$v) {
            $this->assertEquals($v, $env->get($k));
        }
    }
}
