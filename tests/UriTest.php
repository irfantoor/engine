<?php

use IrfanTOOR\Engine\Http\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    function getUri($url = null)
    {
        return new Uri($url);
    }

    function testUriInstance()
    {
        $uri = $this->getUri();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Uri::class, $uri);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $uri);
    }

    function testDefault()
    {
        $uri = $this->getUri();

        $this->assertEquals('', $uri->get('scheme'));
        $this->assertEquals('', $uri->get('user'));
        $this->assertEquals('', $uri->get('pass'));
        $this->assertEquals('', $uri->get('host'));
        $this->assertEquals(null, $uri->get('port'));
        $this->assertEquals('', $uri->get('query'));
        $this->assertEquals('', $uri->get('fragment'));
        $this->assertEquals('', $uri->get('userinfo'));
        $this->assertEquals('', $uri->get('authority'));
        $this->assertEquals('', $uri->get('basepath'));
    }

    function testParsing()
    {
        $uri = $this->getUri("https://user:password@sub.host.com:8080/path/to/some/place/?hello=world#one");

        $this->assertEquals('https', $uri->get('scheme'));
        $this->assertEquals('user', $uri->get('user'));
        $this->assertEquals('password', $uri->get('pass'));
        $this->assertEquals('sub.host.com', $uri->get('host'));
        $this->assertEquals(8080, $uri->get('port'));
        $this->assertEquals('hello=world', $uri->get('query'));
        $this->assertEquals('one', $uri->get('fragment'));
        $this->assertEquals('user:password', $uri->get('userinfo'));
        $this->assertEquals('user:password@sub.host.com:8080', $uri->get('authority'));
        $this->assertEquals('path/to/some/place', $uri->get('basepath'));
    }
}
