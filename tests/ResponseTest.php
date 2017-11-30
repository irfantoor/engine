<?php

use IrfanTOOR\Environment;
use IrfanTOOR\Exception;
use IrfanTOOR\Headers;
use IrfanTOOR\Response;
use IrfanTOOR\Uri;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    function getResponse($status=200, $body='')
    {
        return new Response($status, $body);
    }

    function testResponseInstance()
    {
        $res = $this->getResponse();
        $this->assertInstanceOf('IrfanTOOR\Response', $res);
        $this->assertInstanceOf('IrfanTOOR\Collection', $res);
    }

    function testHeaders()
    {
        $res = $this->getResponse();
        $this->assertInstanceOf('IrfanTOOR\Headers', $res['headers']);
    }

    function testDefaults()
    {
        $res = $this->getResponse();

        $this->assertEquals(200, $res['status']);
        $this->assertEquals('', $res['body']);
        $this->assertEquals($_COOKIE, $res['cookie']);
    }

    function testParameterInitialization()
    {
        $res = New Response(404,'Not Found');

        $this->assertEquals(404, $res['status']);
        $this->assertEquals('Not Found', $res['body']);
    }

    function testCookie()
    {
        $res = $this->getResponse();

        $this->assertEquals(['-- todo'], $res['cookie']);
    }
}
