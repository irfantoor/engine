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
	    $cookies = Cookie::createFromArray(['hello' => 'world']);
	    foreach($cookies as $cookie)
    	    $this->assertInstanceOf('IrfanTOOR\Engine\Http\Cookie', $cookie);
	}
}