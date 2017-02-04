<?php
 
use IrfanTOOR\Engine\Container;
 
class ContainerTest extends PHPUnit_Framework_TestCase 
{
	private $container;

	public function setup() {
		$this->container = new Container([
			'hello' => 'world!',
			'null' => null,
			'array' => ['an' , 'array'],
		]);
	}

	public function testContainerClassExists()
	{
	    $this->assertInstanceOf('IrfanTOOR\Engine\Container', $this->container);
	}

	public function testHas()
	{
		$this->assertTrue($this->container->has('hello'));
		$this->assertTrue($this->container->has('null'));
		$this->assertTrue($this->container->has('array'));
		$this->assertFalse($this->container->has('world!'));
		$this->assertFalse($this->container->has(null));
	}

	public function testGet()
	{
		$this->assertEquals('world!', $this->container->get('hello'));
		$this->assertNull($this->container->get('null'));
		$this->assertEquals(['an','array'], $this->container->get('array'));
	}

	public function testIdNotStringException()
	{

		$this->expectException(IrfanTOOR\Engine\Exception\IdNotStringException::class);
		$this->expectExceptionMessage('Identity , was not a string');
		$world = $this->container->get(null);

		$this->expectException(IrfanTOOR\Engine\Exception\NotFoundException::class);
		$this->expectExceptionMessage('No entry was found for this identifier: has');
		$world = $this->container->get('has');
	}
}
