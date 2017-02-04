<?php
 
use IrfanTOOR\Engine\Container;
 
class ContainerTest extends PHPUnit_Framework_TestCase 
{
	private $container;

	public function setup() {
		$this->container = new Container([
			'defined' => 'defined',
			'null' => null,
			'array' => [
				'k1' => 'v1',
				'k2' => 'v2'
			],
		]);
	}

	public function testContainerClassExists()
	{
	    $this->assertInstanceOf('IrfanTOOR\Engine\Container', $this->container);
	}

	public function testContainerHasAnEntry()
	{
		$this->assertTrue($this->container->has('defined'));
		$this->assertTrue($this->container->has('null'));
		$this->assertTrue($this->container->has('array'));
		$this->assertFalse($this->container->has('not-defined'));

		$this->assertFalse($this->container->has(null));
		$this->assertFalse($this->container->has($this));
	}

	public function testContainerGetEntry()
	{
		$this->assertEquals('defined', $this->container->get('defined'));
		$this->assertNull($this->container->get('null'));
		$this->assertArrayHasKey('k1', $this->container->get('array'));
		$this->assertEquals('v1', $this->container->get('array')['k1']);

		$this->assertArrayHasKey('k2', $this->container->get('array'));
		$this->assertEquals('v2', $this->container->get('array')['k2']);


		# $this->assertEquals('v1', $this->container->get('array.k1'));
		# $this->assertEquals('v2', $this->container->get('array.k2'));
	}

	public function testContainerGetExceptions()
	{

		# IdNotStringException
		$this->expectException(IrfanTOOR\Engine\Exception\IdNotStringException::class);
		$this->expectExceptionMessage('Identity , was not a string');
		$world = $this->container->get(null);

		# NotFoundException
		$this->expectException(IrfanTOOR\Engine\Exception\NotFoundException::class);
		$this->expectExceptionMessage('No entry was found for this identifier: not-defined');
		$exception = $this->container->get('not-defined');
	}

	public function testContainerSetAnEntry()
	{
		# define an entry not previously defined
		$this->assertFalse($this->container->has('not-defined'));

		$this->container->set('not-defined');
		$this->assertTrue($this->container->has('not-defined'));
		$this->assertNull($this->container->get('not-defined'));

		# define an entry previously defined
		$this->container->set('not-defined', 'now-defined');
		$this->assertTrue($this->container->has('not-defined'));
		$this->assertEquals('now-defined', $this->container->get('not-defined'));

		#return $this->container;
	}

	/**
     * @ depends testContainerSetAnEntry
     */
	public function testContainerRemoveAnEntry()
	{
		# define an entry not previously defined
		$this->assertFalse($this->container->has('not-defined'));
		$this->container->set('not-defined', 'now-defined');
		$this->assertEquals('now-defined', $this->container->get('not-defined'));

		$this->container->remove('not-defined');
		# NotFoundException
		$this->expectException(IrfanTOOR\Engine\Exception\NotFoundException::class);
		$this->expectExceptionMessage('No entry was found for this identifier: not-defined');
		$exception = $this->container->get('not-defined');
	}	
}
