<?php

use PHPUnit\Framework\TestCase;

use IrfanTOOR\Engine\Http\Cookie;

class CookieTest extends TestCase 
{
	public function setup(): void
	{
	}
	
    public function testInstanceOfCookie(): void
	{
	    $cookie = new Cookie(['name'  => 'hello', 'value' => 'world']);
    	$this->assertInstanceOf('IrfanTOOR\Engine\Http\Cookie', $cookie);
	}
	
    public function testCookieValues(): void
	{
	    $cookie = new Cookie(['name'  => 'hello', 'value' => 'world']);
	    $this->assertEquals('hello', $cookie->get('name'));
    	$this->assertEquals('world', $cookie->get('value'));
	}
}
