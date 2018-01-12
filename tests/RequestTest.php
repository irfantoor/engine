<?php

use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Factory;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Engine\Http\Validate;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    function getRequest($env=[])
    {
        return Request::create();
    }

    function testRequestInstance()
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(
            IrfanTOOR\Engine\Http\Request::class,
            $request
        );
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Message::class, $request);
    }

    // function testRequestHeaders()
    // {
    //     $request = $this->getRequest();
    //     $this->assertTrue(is_array($request->getHeaders()));
    //     foreach($request->getHeaders() as $k => $v) {
    //         $this->assertTrue(is_array($v));
    //     }
    // }

    function testDefaultRequestMethod()
    {
        $request = $this->getRequest();
        $this->assertEquals(Request::METHOD_GET, $request->getMethod());
    }

    function testRequestWithMethod() {
        $r = $this->getRequest();
        $request = $r->withMethod('POST');
        $this->assertEquals(Request::METHOD_POST, $request->getMethod());
        $this->assertNotSame($r, $request);
    }

    function testRequestUri()
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(
            IrfanTOOR\Engine\Http\Uri::class,
            $request->getUri()
        );
    }

    function testRequestWithUri()
    {
        $uri = Uri::createFromEnvironment();
        $request = $this->getRequest();
        $uri = $request->getUri();
        $request2 = $request->withUri($uri);

        $this->assertNotSame($request, $request2);
    }
}
