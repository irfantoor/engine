<?php

use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Factory;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\ServerRequest;
use IrfanTOOR\Engine\Http\Uri;

use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    function getRequest($env=[])
    {
        return Factory::createServerRequest();
    }

    function testServerRequestInstance()
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(
            IrfanTOOR\Engine\Http\ServerRequest::class,
            $request
        );
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Request::class, $request);
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Message::class, $request);
    }
}
