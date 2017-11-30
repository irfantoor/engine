<?php

use IrfanTOOR\Collection;
use PHPUnit\Framework\TestCase;

class CllectionTest extends TestCase
{
    function getCollection($init = null)
    {
        if (!$init)
            $init = [
                'null'  => NULL,
                'hello' => 'world!',
            ];

        return new Collection($init);
    }

    function testCollectionInstance()
    {
        $c = $this->getCollection();
        $this->assertInstanceOf('IrfanTOOR\Collection', $c);
    }

    function testHas() {
        $c = $this->getCollection();

        # defined elements
        $this->assertTrue($c->has('null'));
        $this->assertTrue($c->has('hello'));
        $this->assertTrue(isset($c['null']));

        # undefined elements
        $this->assertFalse($c->has('nothing'));
        $this->assertFalse(isset($c['nothing']));
    }

    function testGet() {
        $c = $this->getCollection();

        # defined elements
        $this->assertEquals(null, $c->get('null'));
        $this->assertEquals('world!', $c->get('hello'));

        $this->assertEquals(null, $c['null']);
        $this->assertEquals('world!', $c['hello']);

        # undefined elements
        $this->assertEquals(null, $c->get('something'));
        $this->assertEquals(null, $c->get('undefined'));

        $this->assertEquals(null, $c['something']);
        $this->assertEquals(null, $c['undefined']);

        # default behaviour
        $this->assertEquals(null, $c->get('null', 'default'));
        $this->assertEquals('world!', $c->get('hello', 'now-default'));

        $this->assertEquals('default', $c->get('something', 'default'));
        $this->assertEquals('now-default', $c->get('undefined', 'now-default'));
    }



    function testSet()
    {
        $c = $this->getCollection();

        $this->assertEquals(null, $c->get('something'));
        $this->assertEquals(null, $c['something']);
        $this->assertEquals('default', $c->get('something', 'default'));

        # set for the first time
        $c->set('something', 'defined');

        $this->assertEquals('defined', $c->get('something'));
        $this->assertEquals('defined', $c->get('something', 'default'));
        $this->assertEquals('defined', $c['something']);

        # assign a new value
        $c->set('something', 'somethingelse');
        $this->assertEquals('somethingelse', $c->get('something', 'default'));

        # lock the collection
        $c->lock();
        $c->set('something', 'trythis');
        $this->assertEquals('somethingelse', $c->get('something', 'default'));
    }

    function testSetArray()
    {
        $c = $this->getCollection();

        $this->assertEquals(null, $c['something']);
        $this->assertEquals(null, $c['undefined']);

        $c->set([
            'something' => 'defined',
            'undefined' => 'now-defined'
        ]);

        $this->assertEquals('defined', $c['something']);
        $this->assertEquals('now-defined', $c->get('undefined', 'default'));
    }

    function testRemove()
    {
        $c = $this->getCollection();

        $this->assertTrue($c->has('null'));
        $this->assertTrue($c->has('hello'));

        # remove an element
        $c->remove('null');
        $this->assertFalse($c->has('null'));
        $this->assertTrue($c->has('hello'));

        # lock the collection
        $c->lock();
        $c->remove('hello');
        $this->assertTrue($c->has('hello'));
    }

    function testToArray()
    {
        $init = [
            'null'  => NULL,
            'hello' => 'world!',
            'array' => [
                'a' => 'A',
                'b' => 'B'
            ]
        ];

        $c = $this->getCollection($init);
        $a = $c->toArray();

        $this->assertEquals($init, $a);
    }
}
