<?php
 
use IrfanTOOR\Engine\Container;
 
class ContainerTest extends PHPUnit_Framework_TestCase 
{
	public function testContainerClassExists()
	{
		$c = new Container;
	    $this->assertInstanceOf('IrfanTOOR\Engine\Container', $c);
	}

	/**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Identifier "key" is not defined
     */
	public function testGetTheDefaultValue() {
		$c = new Container;

		$this->assertEquals('not-defined', $c->get('key', 'not-defined'));
		$this->assertEquals(null, $c->get('key'));

		# creates expected exception
		$this->assertEquals('value', $c['key']);		
	}

	public function testGetTheDefinedValue() {
		$c = new Container;
		$c['key'] = 'value';

		$this->assertEquals('value', $c->get('key', 'not-defined'));
		$this->assertEquals('value', $c->get('key'));
		$this->assertEquals('value', $c['key']);
	}

	public function testSetAValue() {
		$c = new Container;
		$c->set('key', 'value');

		$this->assertEquals('value', $c->get('key', 'not-defined'));
		$this->assertEquals('value', $c->get('key'));
		$this->assertEquals('value', $c['key']);
	}

	public function testSetMultipleValues() {
		$c = new Container;
		$c->set([
			'key'=>'value', 
			'key2'=>'value2'
		]);

		$this->assertEquals('value', $c->get('key', 'not-defined'));
		$this->assertEquals('value', $c->get('key'));
		$this->assertEquals('value', $c['key']);

		$this->assertEquals('value2', $c->get('key2', 'not-defined'));
		$this->assertEquals('value2', $c->get('key2'));
		$this->assertEquals('value2', $c['key2']);
	}

	public function testHasValue() {
		$c = new Container;
		$c->set('key', 'value');

		$this->assertEquals(true, $c->has('key'));
		$this->assertEquals(false, $c->get('key2'));
	}

	/**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Identifier "key" is not defined
     */
	public function testClearValue() {
		$c = new Container;
		$c->set('key', 'value');
		$c->clear('key');

		$this->assertEquals(false, $c->has('key'));
		$this->assertEquals(null, $c->get('key'));
		$this->assertEquals('not-defined', $c->get('key', 'not-defined'));
		# Raises exception
		$this->assertEquals('not-defined', $c['key']);		
	}
}
