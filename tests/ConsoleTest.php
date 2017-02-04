<?php
 
use IrfanTOOR\Engine\Console;
 
class ConsoleTest extends PHPUnit_Framework_TestCase 
{

	protected $console;

	public function setup() {
		$this->console = new Console;
	}

	public function testConsoleClassExists()
	{
	    $this->assertInstanceOf('IrfanTOOR\Engine\Console', $this->console);
	}

	public function testConsoleWrite()
	{
		$c = $this->console;

	    ob_start();
	    $c->write('Hello World!');
	    $actual = ob_get_clean();
	    
	    $this->assertEquals('Hello World!', $actual);

	    ob_start();
	    $c->writeln('Hello World!');
	    $actual = ob_get_clean();

	    $this->assertEquals('Hello World!' . PHP_EOL, $actual);
	}

	public function testConsoleWriteWithStyle()
	{
	    $c = $this->console;

	    ob_start();
	    	$c->write('Hello World!', 'red');
	    $actual = ob_get_clean();

	    ob_start();
            $c->style('red');
            echo 'Hello World!';
            $c->style();
        $expected = ob_get_clean();
          
	    $this->assertEquals($expected, $actual);
	}
}
