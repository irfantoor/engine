<?php

use IrfanTOOR\Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    function testExceptionInstance()
    {
        try {
            throw new Exception('Test Exception', 1);
        }
        catch(Exception $e) {
        }

        $this->assertInstanceOf('IrfanTOOR\Exception', $e);
        $this->assertEquals('Test Exception', $e->getMessage());
    }
}
