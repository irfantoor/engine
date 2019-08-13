<?php

use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Test;

class EnvironmentTest extends Test
{
    function testCollectionInstance()
    {
        $e = new Environment();
        $this->assertInstanceOf(Environment::class, $e);
        $this->assertInstanceOf(Collection::class, $e);
    }

    function testServerParameters()
    {
        $env = new Environment();

        foreach ($_SERVER as $k=>$v) {
            $this->assertEquals($v, $env->get($k));
        }
    }

    function testMockingEnv()
    {
        # $this->assertEquals('to be mocked $data must be an array', $e->getMessage());
        $env = new Environment();

        $mock = [
            'REQUEST_TIME' => 0,
            'Hello'        => 'World!',
        ];

        $env = new Environment($mock);

        $mocked_env = array_merge($_SERVER, $mock);

        # Mocked Environment variables are added/modified
        foreach ($mocked_env as $k => $v) {
            $this->assertEquals($v, $env->get($k));
        }
    }

    function testLockedEnvironment()
    {
        $mock = [
            'REQUEST_TIME' => 0,
            'Hello'        => 'World!',
        ];

        $env = new Environment($mock);
        $env->set('Hello', 'Someone');
        $this->assertEquals('World!', $env->get('Hello'));
        $env->remove('Hello');
        $this->assertEquals('World!', $env->get('Hello'));
    }
}
