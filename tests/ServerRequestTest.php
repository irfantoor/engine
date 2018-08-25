<?php

use IrfanTOOR\Engine\Http\Message;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\ServerRequest;

use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    function getServerRequest($env=[])
    {
        return new ServerRequest($env);
    }

    function testServerRequestInstance()
    {
        $request = $this->getServerRequest();
        $this->assertInstanceOf(
            IrfanTOOR\Engine\Http\ServerRequest::class,
            $request
        );
        $this->assertInstanceOf(
            Psr\Http\Message\ServerRequestInterface::class, 
            $request
        );        
    }
    
    function testServerEnvironment()
    {
        $request = $this->getServerRequest();
        
        $env = $request->getServerParams();
        $this->assertTrue(is_array($env));
        $this->assertEquals('GET', $env['REQUEST_METHOD']);
        
    }
    
    # todo -- test the default values inherit from environment
    function testRequestCloning()
    {
        $r1 = $this->getServerRequest();
        $r2 = clone $r1;
        
        $this->assertEquals($r2, $r1);
        $this->assertNotSame($r2, $r1);
    }
}
