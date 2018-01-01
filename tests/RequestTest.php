<?php

use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\RequestMethod;
use IrfanTOOR\Engine\Http\Uri;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    function getRequest($env=[])
    {
        return Request::createFromEnvironment();
    }

    function testRequestInstance()
    {
        $req = $this->getRequest();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Request::class, $req);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $req);
    }

    function testRequestHeaders()
    {
        $req = $this->getRequest();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Headers::class, $req['headers']);
    }

    function testRequestUri()
    {
        $req = $this->getRequest();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Uri::class, $req['uri']);
    }

    function testRequestMethod()
    {
        $req = $this->getRequest();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\RequestMethod::class, $req['method']);
        $this->assertEquals('GET', $req['method']->getMethod());
    }

    function testDefaultRequestMethod()
    {
        $req = $this->getRequest();
        $this->assertEquals(RequestMethod::METHOD_GET, $req['method']->__toString());
    }


    function testRequestWith() {
        # $req = with('method', RequestMethod::METHOD_POST);
        # $this->assertEquals(RequestMethod::METHOD_POST, $req['method']->__toString());
        $this->assertEquals('', 'todo -- with mothod needs to be defined');
    }

    function testRequestCookie()
    {
        $req = $this->getRequest();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Cookie::class, $req['cookie']);
    }

    function testOtherValues()
    {
        $env = new Environment();

        $req = $this->getRequest();
        $this->assertEquals(null, $req['body']);
        $this->assertEquals($_GET, $req['get']);
        $this->assertEquals($_POST, $req['post']);
        $this->assertEquals($_FILES, $req['files']);
        $this->assertEquals($env['REMOTE_ADDR'], $req['ip']);
        $this->assertEquals($env['REQUEST_TIME_FLOAT'], $req['time']);
    }
}
