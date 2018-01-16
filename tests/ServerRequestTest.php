<?php

use IrfanTOOR\Engine\Http\Message;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\ServerRequest;

use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    function getRequest($env=[])
    {
        return new ServerRequest($env);
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
