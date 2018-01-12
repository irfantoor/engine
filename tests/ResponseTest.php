<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamIterface;
use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Factory;
use IrfanTOOR\Engine\Http\Factory\ResponseFactory;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Http\Stream;
use IrfanTOOR\Engine\Http\ResponseStatus;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    function getResponse(
        $status  = Response::STATUS_OK,
        $headers = null,
        $body    = null
    ){
        $response = IrfanTOOR\Engine\Http\Factory::createResponse();
        return new Response($status, $headers, $body);
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
        $this->assertEquals(Response::STATUS_OK, $response->getStatusCode());
    }

    function testHeaders()
    {
        $response = $this->getResponse();
        $this->assertTrue(is_array($response->getHeaders()));
        $response = $response->withHeader('alfa', 'beta');
        $this->assertEquals(['alfa' => ['beta']], $response->getHeaders());
    }

    function testBody()
    {
        $response = $this->getResponse();
        $this->assertInstanceOf(
            Psr\Http\Message\StreamInterface::class,
            $response->getBody()
        );
    }

    function testDefaults()
    {
        $response = $this->getResponse();

        $this->assertEquals(Response::STATUS_OK, $response->getStatusCode());
        $this->assertInstanceOf(
            Psr\Http\Message\StreamInterface::class,
            $response->getBody()
        );
    }

    function testParameterInitialization()
    {
        $response = Response::create(
            [
                'code' => Response::STATUS_NOT_FOUND,
                'body' => 'Hello World!',
            ]
        );

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatusCode()
        );

        $this->assertEquals(
            'Hello World!',
            (string) $response->getBody()
        );
    }
}
