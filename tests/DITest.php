<?php

use IrfanTOOR\Test;
use IrfanTOOR\Engine;
use IrfanTOOR\Container;
use IrfanTOOR\Engine\DI;

class DITest extends Test
{
    function getEngine($init = [])
    {
        # If we wont set it, it might make the this class die silently!
        if (!isset($init['status']))
            $init['status'] = Engine::STATUS_OK;

        if (!isset($init['debug']))
            $init['debug'] = ['level' => 2];

        return new Engine($init);
    }

    function testInstance()
    {
        $di = new DI();
        $this->assertInstanceOf(DI::class, $di);
    }

    function testLoad()
    {
        $di = new DI();

        $this->assertTrue(method_exists( $di, 'load' ));

        $h = $di->load('Hello');
        $this->assertInstanceOf( Hello::class, $h );
        $this->assertEquals( 'Hello Factory!', $h->greeting());

        $w = $di->load( 'World' );
        $this->assertInstanceOf( World::class, $w );
        $this->assertEquals( 'Hello World!', $w->greeting() );

        $ie = $this->getEngine();
        $this->assertFalse(method_exists( $ie, 'load' ));

        $h = $ie->load( 'Hello' );
        $this->assertInstanceOf( Hello::class, $h );
        $this->assertEquals( 'Hello Factory!', $h->greeting() );
    }

    function testCreate()
    {
        $di = new DI();

        $this->assertTrue(method_exists( $di, 'create' ));

        $h = $di->create('Hello');
        $this->assertInstanceOf( Hello::class, $h );
        $this->assertEquals( 'Hello Factory!', $h->greeting());

        $h = $di->create('Hello', ['args!']);
        $this->assertInstanceOf( Hello::class, $h );
        $this->assertEquals( 'Hello args!', $h->greeting());

        $w = $di->create('World');
        $this->assertInstanceOf( World::class, $w );
        $this->assertEquals( 'Hello World!', $w->greeting());

        $w = $di->create('World', ['My']);
        $this->assertInstanceOf( World::class, $w );
        $this->assertEquals( 'My World!', $w->greeting());
    }

    function testCreateFromEnvironment()
    {
        $di = new DI();

        $this->assertTrue(method_exists( $di, 'createFromEnvironment' ));

        $h = $di->createFromEnvironment('Hello');
        $this->assertInstanceOf( Hello::class, $h );
        $this->assertEquals( 'Hello Environment!', $h->greeting());

        $w = $di->createFromEnvironment('World');
        $this->assertNull( $w );
    }
}

class HelloFactory
{
    function createHello( ?string $dest = null )
    {
        if ($dest)
            return new Hello( $dest );
        else
            return new Hello( 'Factory!' );
    }

    function createFromEnvironment()
    {
        return new Hello( 'Environment!' );
    }
}

class Hello {
    protected $dest;

    function __construct( string $dest = 'World!' )
    {
        $this->dest = $dest;
    }

    function greeting()
    {
        return 'Hello ' . $this->dest;
    }
}

class World {
    protected $greeting;

    function __construct( string $greeting = 'Hello' )
    {
        $this->greeting = $greeting;
    }

    function greeting()
    {
        return $this->greeting . ' ' . 'World!';
    }
}
