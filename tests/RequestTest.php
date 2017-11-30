<?php

use IrfanTOOR\Environment;
use IrfanTOOR\Headers;
use IrfanTOOR\Request;
use IrfanTOOR\Uri;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    function getRequest($env=[])
    {
        return new Request($env);
    }

    function testRequestInstance()
    {
        $req = $this->getRequest();
        $this->assertInstanceOf('IrfanTOOR\Request', $req);
        $this->assertInstanceOf('IrfanTOOR\Collection', $req);
    }

    function testHeaders()
    {
        $req = $this->getRequest();
        $this->assertInstanceOf('IrfanTOOR\Headers', $req['headers']);
    }

    function testUri()
    {
        $req = $this->getRequest();
        $this->assertInstanceOf('IrfanTOOR\Uri', $req['uri']);
    }

    function testOtherKeys()
    {
        $req = $this->getRequest();

        $this->assertEquals('', $req['method']);
        $this->assertEquals(null, $req['body']);
        $this->assertEquals($_GET, $req['get']);
        $this->assertEquals($_POST, $req['post']);
        $this->assertEquals($_COOKIE, $req['cookie']);
        $this->assertEquals($_FILES, $req['files']);
        $this->assertEquals($_SERVER['REMOTE_ADDR'], $req['ip']);
        $this->assertEquals($_SERVER['REQUEST_TIME_FLOAT'], $req['time']);
    }
}
