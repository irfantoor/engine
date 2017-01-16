<?php
 
use IrfanTOOR\Engine\Console;
 
class ConsoleTest extends PHPUnit_Framework_TestCase 
{
	public function testConsoleClassExists()
	{
		$c = new Console;
	    $this->assertInstanceOf('IrfanTOOR\Engine\Console', $c);
	}

	public function testConsoleOut()
	{
	    $c = new Console;
	    ob_start();
	    $c->out('Hello World!');
	    $actual = ob_get_clean();
	    $this->assertEquals('Hello World!', $actual);
	}

	
	public function testConsoleColoredOut()
	{
	    $c = new Console;
	    ob_start();
	    	$c->out('Hello World!', 'red');
	    $actual = ob_get_clean();

	    ob_start();
            $c->escape('red');
            echo 'Hello World!';
            $c->escape('');
        $expected = ob_get_clean();
          
	    $this->assertEquals($expected, $actual);
	}

	public function testShowsHelpOnNoCommandOption()
	{
	    $c = new Console;
	    ob_start();
	    	$c->run();
	    $actual = ob_get_clean();

	    ob_start();
	    	$c->help();
	    $expected = ob_get_clean();

	    $this->assertEquals($expected, $actual);
	}

	public function testShowsHelpIfHelpIsRequested()
	{
	    $c = new Console;
	    $_SERVER['argv'][1] = '-h';

	    ob_start();
	    	$c->run();
	    $actual = ob_get_clean();

	    ob_start();
	    	$c->help();
	    $expected = ob_get_clean();

	    $this->assertEquals($expected, $actual);

	    $c = new Console;
	    $_SERVER['argv'][1] = '--help';
	    ob_start();
	    	$c->run();
	    $actual = ob_get_clean();
	    
	    $this->assertEquals($expected, $actual);
	}

	public function testVersionCommand()
	{
	    $c = new Console('ABC','X.Y.Z');
	    $_SERVER['argv'][1] = '-V';

	    ob_start();
	    	$c->run();
	    $actual = ob_get_clean();

	    ob_start();
	    	$c->version();
	    $expected = ob_get_clean();

	    $this->assertEquals($expected, $actual);

	    $c = new Console('ABC','X.Y.Z');
	    $_SERVER['argv'][1] = '--version';
	    ob_start();
	    	$c->run();
	    $actual = ob_get_clean();
	    
	    $this->assertEquals($expected, $actual);
	}
}
