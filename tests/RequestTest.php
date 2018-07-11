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
        return new Request();
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

    function testRequestHeaders()
    {
        $request = $this->getRequest();
        $headers = $request->get('headers');
        $this->assertInstanceOf(
            IrfanTOOR\Engine\Http\Headers::class, 
            $headers
        );
        $this->assertTrue(is_array($headers->toArray()));
        
        foreach($headers as $k => $v) {
            $this->assertTrue(is_array($v));
        }
    }

    function testDefaultRequestMethod()
    {
        $request = $this->getRequest();
        $this->assertEquals('GET', $request->get('method'));
    }

    function testRequestWithMethod() {
        $request = $this->getRequest();
        $request->set('method', 'POST');
        $this->assertEquals('POST', $request->get('method'));
    }

    function testRequestUri()
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(
            IrfanTOOR\Engine\Http\Uri::class,
            $request->get('uri')
        );
    }

    function testRequestClone()
    {
        $request1 = $this->getRequest();
        $request2 = clone $request1;
        $this->assertEquals($request1, $request2);
        $this->assertNotSame($request1, $request2);
        
        $uri1 = $request1->get('uri');
        $uri2 = $request2->get('uri');
        $this->assertEquals($uri1, $uri2);
        $this->assertNotSame($uri1, $uri2);

        $h1 = $request1->get('headers');
        $h2 = $request2->get('headers');
        $this->assertEquals($h1, $h2);
        $this->assertNotSame($h1, $h2);
    }
}
