<?php

use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Test;

class EnvironmentTest extends Test
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
        $this->assertException(
            function(){
                $mock = null;
                $env = new Environment($mock);
            }, 
            Exception::class, 
            'to be mocked $data must be an array'
        );
        
        # $this->assertEquals('to be mocked $data must be an array', $e->getMessage());

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
