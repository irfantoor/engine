<?php

use IrfanTOOR\Engine\Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    function testExceptionInstance()
    {
        try {
            throw new Exception('Test Exception');
        }
        catch(Exception $e) {
        }

        $this->assertInstanceOf(IrfanTOOR\Engine\Exception::class, $e);
        $this->assertEquals('Test Exception', $e->getMessage());
    }
}
