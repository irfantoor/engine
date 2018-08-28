<?php

use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Http\Stream;
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

    function testExceptionLog()
    {
        # todo -- in case of empty parameter, the log is send to /dev/stderr
        Exception::log();
        try {
            $l = __LINE__; throw new Exception('Test Exception Log', 1);
        }
        catch(Exception $e) {
        }
        
        $this->assertEquals('Test Exception Log', $e->getMessage());
//         $this->assertEquals(
//             date('Y-m-d H:i:s') . 
//             ' LEVEL-1 Test Exception, file: tests/ExceptionTest.php, line: ' . 
//             $l . "\n", 
//             
//             readfrom('/dev/stderror')
//         );
    }

    function testExceptionLogFile()
    {
        $file = dirname(__FILE__) . '/folder/log.txt';
        if (file_exists($file))
            unlink($file);

        Exception::log($file);
        
        try {
            $l = __LINE__; throw new Exception('Test Exception Log File', 1);
        }
        catch(Exception $e) {
        }
        
        $this->assertEquals('Test Exception Log File', $e->getMessage());
        $this->assertEquals(
            date('Y-m-d H:i:s') . 
            ' LEVEL-1 Test Exception Log File, file: tests/ExceptionTest.php, line: ' . 
            $l . "\n", 
            
            file_get_contents($file)
        );
        
        unlink($file);
    }
}
