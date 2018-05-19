<?php

use IrfanTOOR\Engine\Session;
use IrfanTOOR\Engine\Http\ServerRequest;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    function testSessionInstance()
    {
        $r = new ServerRequest;
        $s = new Session($r);
        $this->assertInstanceOf(IrfanTOOR\Engine\Session::class, $s);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $s);
    }
}
