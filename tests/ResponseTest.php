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
    }

    function testDefaultResponseStatus()
    {
        $response = $this->getResponse();
        $status = $response->get('status');
        $this->assertEquals(200, $status);
        $this->assertEquals('OK', $response->phrase($status));
    }

    function testHeaders()
    {
        $response = $this->getResponse();
        $headers = $response->get('headers');
        $this->assertInstanceOf(
            IrfanTOOR\Engine\Http\Headers::class,
            $headers
        );
        
        foreach($headers as $k => $v) {
            $this->assertTrue(is_array($v));
        }

        $response->get('headers')->set('alfa', 'beta');
        $this->assertEquals(['beta'], $response->get('headers')->get('ALFA'));
        $this->assertEquals(
            ['alfa' =>['beta']], 
            $response->get('headers')->toArray()
        );
    }

    function testBody()
    {
        $response = $this->getResponse();
        $this->assertEquals('', $response->get('body'));
    }

    function testDefaults()
    {
        $response = $this->getResponse();
        
        $this->assertEquals('1.1', $response->get('version'));
        $this->assertEquals(200, $response->get('status'));
        $this->assertEquals([], $response->get('headers')->toArray());
        $this->assertEquals('', $response->get('body'));
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
        $this->assertEquals('Hello', $response->get('body'));
        $response->write(' ');
        $this->assertEquals('Hello ', $response->get('body'));
        $response->write('World!');
        $this->assertEquals('Hello World!', $response->get('body'));
    }
}
