<?php

use IrfanTOOR\Engine\Session;
use IrfanTOOR\Engine\Http\ServerRequest;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    function getSession()
    {
        $request = new ServerRequest;
        return new Session($request);
    }
    
    function testSessionInstance()
    {
        $s = $this->getSession();
        $this->assertInstanceOf(IrfanTOOR\Engine\Session::class, $s);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $s);
    }
    
    function testSessionAddAValue()
    {
        $s = $this->getSession();
        $s->set('hello', 'world');
        $s->set('logged', true);
        
        $this->assertEquals('world', $s->get('hello'));
        $this->assertEquals(true, $s->get('logged'));
        
        $s->save();
    }
    
    function testSessionSavedValue()
    {
        $s = $this->getSession();
        $this->assertEquals('world', $s->get('hello'));
        $this->assertEquals(true, $s->get('logged'));
    }

    function testSessionRemoveAValue()
    {
        $s = $this->getSession();
        $s->remove('hello');
        $this->assertNull($s->get('hello'));
        $this->assertEquals(true, $s->get('logged'));
    }
    
    function testSessionDestroySession()
    {
        $s = $this->getSession();
        $s->set('hello', 'world');
        $s->destroy();
        $this->assertNull($s->get('hello'));
        $this->assertNull($s->get('logged'));
    }
}
