<?php
 
use IrfanTOOR\Engine\Console;
 
class ConsoleTest extends PHPUnit_Framework_TestCase 
{ 
	public function testConsoleOut()
	{
	    $console = new Console;
	    ob_start();
	    $console->out('Hello World!');
	    $txt = ob_get_clean();
	    $this->assertEquals('Hello World!', $txt);
	}

	
	public function testConsoleColoredOut()
	{
	    $console = new Console;
	    ob_start();
	    	$console->out('Hello World!', 'red');
	    $txt = ob_get_clean();

	    ob_start();
            $console->escape('red');
            echo 'Hello World!';
            $console->escape('');
        $txt2 = ob_get_clean();
          
	    $this->assertEquals($txt2, $txt);
	}

	public function testShowsHelpOnNoCommandOption()
	{
	    $console = new Console('ABCD', 'X.Y.Z');
	    ob_start();
	    	$console->run();
	    $txt = ob_get_clean();

	    ob_start();
	    	$console->help();
	    $txt2 = ob_get_clean();

	    $this->assertEquals($txt2, $txt);
	}

	public function testShowsHelpIfHelpIsRequested()
	{
	    $console = new Console('ABCD', 'X.Y.Z');

	    $_SERVER['argv'][1] = '-h';

	    ob_start();
	    	$console->run();
	    $txt = ob_get_clean();

	    ob_start();
	    	$console->help();
	    $txt2 = ob_get_clean();

	    $console = new Console('ABCD', 'X.Y.Z');
	    $_SERVER['argv'][1] = '--help';
	    ob_start();
	    	$console->run();
	    $txt3 = ob_get_clean();

	    $this->assertEquals($txt2, $txt);
	    $this->assertEquals($txt3, $txt);
	}

	public function testVersionCommand()
	{
	    $console = new Console();
	    $_SERVER['argv'][1] = '-V';

	    ob_start();
	    	$console->run();
	    $txt = ob_get_clean();

	    ob_start();
	    	$console->version();
	    $txt2 = ob_get_clean();

	    $console = new Console();
	    $_SERVER['argv'][1] = '--version';
	    ob_start();
	    	$console->run();
	    $txt3 = ob_get_clean();

	    $this->assertEquals($txt2, $txt);
	    $this->assertEquals($txt3, $txt);
	}
}
