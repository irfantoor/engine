<?php

use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Engine\Http\Factory;
use Psr\Http\Message\UriInterface;


use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    function getUri($url = '')
    {
        #return new Uri($url);
        return new Uri($url);
    }

    function testUriInstance()
    {
        $uri = $this->getUri();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Uri::class, $uri);
        $this->assertInstanceOf(Psr\Http\Message\UriInterface::class, $uri);
    }

    function testDefault()
    {
        $uri = $this->getUri();

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
        $this->assertEquals('', $uri->getAuthority());
    }

    function testParsing()
    {
        $uri = $this->getUri("https://user:password@sub.host.com:8080/path/to/some/place/?hello=world#one");

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user:password', $uri->getUserInfo());
        $this->assertEquals('sub.host.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertEquals('/path/to/some/place/', $uri->getPath());
        $this->assertEquals('hello=world', $uri->getQuery());
        $this->assertEquals('one', $uri->getFragment());
        $this->assertEquals('user:password@sub.host.com:8080', $uri->getAuthority());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWhenUriIsInvalid()
    {
        $this->getUri(':');
    }

}
