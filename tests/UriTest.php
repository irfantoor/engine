<?php

use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Test;

class UriTest extends Test
{
    function testUriInstance()
    {
        $uri = new Uri;
        $this->assertInstanceOf(Uri::class, $uri);
    }

    function testDefault()
    {
        $uri = new Uri;

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
        $this->assertEquals('', $uri->getAuthority());

        $this->assertEquals('', (string) $uri);
    }

    function testHttpsDefault()
    {
        $url = 'https://example.com/';
        $uri = new Uri($url);

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
        $this->assertEquals('example.com', $uri->getAuthority());

        $this->assertEquals($url, (string) $uri);
    }

    function testScheme()
    {
        $uri = new Uri('example.com');
        $this->assertEquals('http', $uri->getScheme());

        $uri = new Uri('http://example.com');
        $this->assertEquals('http', $uri->getScheme());

        $uri = new Uri('ftp://example.com');
        $this->assertEquals('ftp', $uri->getScheme());

        $uri = new Uri('example.com:443');
        $this->assertEquals('https', $uri->getScheme());

        $uri = new Uri('http://example.com:443');
        $this->assertEquals('http', $uri->getScheme());
    }

    function testUserInfo()
    {
        $uri = new Uri('example.com');
        $this->assertEquals('', $uri->getUserInfo());

        $uri = new Uri('http://user@example.com');
        $this->assertEquals('', $uri->getUserInfo());

        $uri = new Uri('http://user:@example.com');
        $this->assertEquals('', $uri->getUserInfo());

        $uri = new Uri('http://:pass@example.com');
        $this->assertEquals('', $uri->getUserInfo());

        $uri = new Uri('http://user:pass@example.com');
        $this->assertEquals('user:pass', $uri->getUserInfo());

        $uri = new Uri('user:pass@example.com');
        $this->assertEquals('user:pass', $uri->getUserInfo());
    }

    function testHost()
    {
        $uri = new Uri('example.com');
        $this->assertEquals('example.com', $uri->getHost());

        $uri = new Uri('example.com:80');
        $this->assertEquals('example.com', $uri->getHost());

        $uri = new Uri('http://example.com');
        $this->assertEquals('example.com', $uri->getHost());

        $uri = new Uri('example.com:443');
        $this->assertEquals('example.com', $uri->getHost());

        $uri = new Uri('http://example.com:443');
        $this->assertEquals('example.com', $uri->getHost());
    }

    function testPort()
    {
        $uri = new Uri('example.com');
        $this->assertEquals(null, $uri->getPort());

        $uri = new Uri('example.com:80');
        $this->assertEquals(null, $uri->getPort());

        $uri = new Uri('http://example.com');
        $this->assertEquals(null, $uri->getPort());

        $uri = new Uri('example.com:443');
        $this->assertEquals(null, $uri->getPort());

        $uri = new Uri('http://example.com:443');
        $this->assertEquals(443, $uri->getPort());

        $uri = new Uri('https://example.com:80');
        $this->assertEquals(80, $uri->getPort());
    }

    function testPath()
    {
        $uri = new Uri('example.com');
        $this->assertEquals('', $uri->getPath());

        $uri = new Uri('example.com:80/');
        $this->assertEquals('/', $uri->getPath());

        $uri = new Uri('http://example.com/hello/world');
        $this->assertEquals('/hello/world', $uri->getPath());

        $uri = new Uri('http://example.com/hello/world/');
        $this->assertEquals('/hello/world/', $uri->getPath());
    }

    function testQuery()
    {
        $uri = new Uri('example.com?');
        $this->assertEquals('', $uri->getQuery());
        $uri = new Uri('example.com?hello_world');
        $this->assertEquals('hello_world', $uri->getQuery());
        $uri = new Uri('example.com?hello=world&go=google');
        $this->assertEquals('hello=world&go=google', $uri->getQuery());
        $uri = new Uri('example.com/?hello=world&go=google');
        $this->assertEquals('hello=world&go=google', $uri->getQuery());
    }

    function testFragment()
    {
        $uri = new Uri('example.com?#');
        $this->assertEquals('', $uri->getFragment());
        $uri = new Uri('example.com?test=again&#hello-world');
        $this->assertEquals('hello-world', $uri->getFragment());
    }

    function testParsing()
    {
        $url = "https://user:password@sub.host.com:8080/path/to/some/place/?hello=world#one";
        $uri = new Uri($url);

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user:password', $uri->getUserInfo());
        $this->assertEquals('sub.host.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertEquals('/path/to/some/place/', $uri->getPath());
        $this->assertEquals('hello=world', $uri->getQuery());
        $this->assertEquals('one', $uri->getFragment());
        $this->assertEquals('user:password@sub.host.com:8080', $uri->getAuthority());

        $this->assertEquals($url, (string) $uri);

        $uri = new Uri("http://user:password@sub.host.com:80/path/to/some/place/?hello=world#one");
        $this->assertNull($uri->getPort());

        $uri = new Uri("https://user:password@sub.host.com:443/path/to/some/place/?hello=world#one");
        $this->assertNull($uri->getPort());
    }

    function testExceptionWhenUriIsInvalid()
    {
        $this->assertException(function(){
            new Uri(':');
        }, 'InvalidArgumentException');
    }
}
