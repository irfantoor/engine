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

		# Set a class
		$class = new testClass('hello');
		$this->container->set('class', $class);

		$c1 = $this->container->get('class');
		$c2 = $this->container->get('class');

		$this->assertInstanceOf('testClass', $c1);
		$this->assertInstanceOf('testClass', $c2);
		$this->assertEquals($c1, $c2);
		$this->assertSame($c1, $c2);
		$this->assertSame($c1->value, $c2->value);


		$this->container->set('closure', function($args) {
			return new testClass($args);
		});

		$c1 = $this->container->get('closure');
		$c2 = $this->container->get('closure');
		$c3 = $this->container->get('closure', 'hello');

		$this->assertInstanceOf('testClass', $c1);
		$this->assertInstanceOf('testClass', $c2);

		$this->assertNull($c1->value());
		$this->assertNull($c2->value());
		$this->assertEquals('hello', $c3->value());

		$this->assertEquals($c1, $c2);
		$this->assertNotSame($c1, $c2);
		$this->assertNotEquals($c1, $c3);

		$c1 = $this->container->get('closure', $this->container);
		$this->assertEquals($this->container, $c1->value());
		$this->assertSame($this->container, $c1->value());

		return $this->container;
	}

	/**
     * @depends testContainerSetAnEntry
     */
	public function testContainerRemoveAnEntry($container)
	{
		$this->container = $container;

		$this->assertTrue($this->container->has('closure'));
		$this->container->remove('closure');

		# NotFoundException
		$this->expectException(IrfanTOOR\Engine\Exception\NotFoundException::class);
		$this->expectExceptionMessage('No entry was found for this identifier: closure');
		$exception = $this->container->get('closure');

		#$this->expectException(null);
		$this->container->set('closure', 'test');
		$this->assertNull($this->container->get('closure'));

		return $this->container;
	}

	/**
	 * @depends testContainerSetAnEntry
     * @depends testContainerRemoveAnEntry
     */
	public function testContainerRemoveAClosure($container)
	{
		$this->container = $container;

		$this->container->set('closure', 'test');
		$this->assertEquals('test', $this->container->get('closure'));
	}


}


class testClass
{
	public $value;

	public function __construct($value) {
		$this->value = $value;
	}

	public function value(){
		return $this->value;
	}
}
