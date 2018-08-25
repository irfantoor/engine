<?php

use IrfanTOOR\Engine\Http\Exception;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Response;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    function getResponse(
        $status  = 200,
        $headers = [],
        $body    = ''
    ){
        return new Response([
            'status'  => $status,
            'headers' => $headers,
            'body'    => $body,
        ]);
    }

    function testResponseInstance()
    {
        $response = $this->getResponse();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Response::class, $response);
        $this->assertInstanceOf(Psr\Http\Message\ResponseInterface::class, $response);
    }

    function testDefaultResponseStatus()
    {
        $response = $this->getResponse();
        $status = $response->getStatusCode();
        $this->assertEquals(200, $status);
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    function testHeaders()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $this->assertTrue(is_array($headers));
        
        foreach($headers as $k => $v) {
            $this->assertTrue(is_array($v));
        }

        $response = $response->withHeader('alfa', 'beta');
        $this->assertEquals(['beta'], $response->getHeader('ALFA'));
        $this->assertEquals(['alfa' =>['beta']], $response->getHeaders());
    }

    function testBody()
    {
        $response = $this->getResponse();
        $this->assertEquals('', $response->getBody());
    }

    function testDefaults()
    {
        $response = $this->getResponse();
        
        $this->assertEquals('1.1', $response->getProtocolVersion());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([], $response->getHeaders());
        $this->assertEquals('', $response->getBody());
    }

//     function testParameterInitialization()
//     {
//         $response = new Response([
//             'status' => 'NOT_FOUND',
//         ]);
// 
//         $this->assertEquals(404, $response->get('status'));
//     }

    function testWrite()
    {
        $response = new Response();
        $response->write('Hello');
        $this->assertEquals('Hello', $response->getBody());
        $response->write(' ');
        $this->assertEquals('Hello ', $response->getBody());
        $response->write('World!');
        $this->assertEquals('Hello World!', $response->getBody());
    }
}
