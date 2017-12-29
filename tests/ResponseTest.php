<?php

use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Http\ResponseStatus;
use IrfanTOOR\Engine\Http\Uri;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    function getResponse($status=200, $headers=[], $body='')
    {
        return new Response($status, $headers, $body);
    }

    function testResponseInstance()
    {
        $res = $this->getResponse();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Response::class, $res);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $res);
    }

    function testResponseStatus()
    {
        $res = $this->getResponse();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\ResponseStatus::class, $res['status']);
    }

    function testHeaders()
    {
        $res = $this->getResponse();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Headers::class, $res['headers']);
        $res = $res->with('header', ['alfa' => 'beta']);
        $this->assertEquals([], $res->get('headers')->toArray());
    }

    function testBody()
    {
        $res = $this->getResponse();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Stream::class, $res['body']);
    }

    function testDefaults()
    {
        $res = $this->getResponse();

        $this->assertEquals(ResponseStatus::STATUS_OK, $res['status']->__toString());
        $this->assertEquals('', $res['body']->__toString());
        $this->assertEquals(null, $res['cookie']);
    }

    function testParameterInitialization()
    {
        $res = New Response(ResponseStatus::STATUS_NOT_FOUND, [], 'Not Found');

        $this->assertEquals(ResponseStatus::STATUS_NOT_FOUND, $res['status']->__toString());
        $this->assertEquals('Not Found', $res['body']->__toString());
    }

    function testCookie()
    {
        $this->assertEquals('', 'todo -- Cookie needs to be implemented');
    }
}
