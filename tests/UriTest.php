<?php

use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Engine\Http\Factory;
use Psr\Http\Message\UriInterface;


use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    function testUriInstance()
    {
        $uri = new Uri;
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Uri::class, $uri);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $uri);
    }

    function testDefault()
    {
        $uri = new Uri;

        $this->assertEquals('http', $uri->get('scheme'));
        $this->assertEquals('', $uri->get('user_info'));
        $this->assertEquals('localhost', $uri->get('host'));
        $this->assertEquals(null, $uri->get('port'));
        $this->assertEquals('/', $uri->get('path'));
        $this->assertEquals('', $uri->get('query'));
        $this->assertEquals('', $uri->get('fragment'));
        $this->assertEquals('localhost', $uri->get('authority'));
    }
    
    function testHttpsDefault()
    {
        $uri = new Uri('https://example.com/');

        $this->assertEquals('https', $uri->get('scheme'));
        $this->assertEquals('', $uri->get('user_info'));
        $this->assertEquals('example.com', $uri->get('host'));
        $this->assertEquals(null, $uri->get('port'));
        $this->assertEquals('/', $uri->get('path'));
        $this->assertEquals('', $uri->get('query'));
        $this->assertEquals('', $uri->get('fragment'));
        $this->assertEquals('example.com', $uri->get('authority'));
    }
    
    function testScheme()
    {
        $uri = new Uri('example.com');
        $this->assertEquals('http', $uri->get('scheme'));
        
        $uri = new Uri('http://example.com');
        $this->assertEquals('http', $uri->get('scheme'));
        
        $uri = new Uri('ftp://example.com');
        $this->assertEquals('ftp', $uri->get('scheme'));
        
        $uri = new Uri('example.com:443');
        $this->assertEquals('https', $uri->get('scheme'));
        
        $uri = new Uri('http://example.com:443');
        $this->assertEquals('http', $uri->get('scheme'));        
    }
    
    function testUserInfo()
    {
        $uri = new Uri('example.com');
        $this->assertEquals('', $uri->get('user_info'));
        
        $uri = new Uri('http://user@example.com');
        $this->assertEquals('', $uri->get('user_info'));

        $uri = new Uri('http://user:@example.com');
        $this->assertEquals('', $uri->get('user_info'));

        $uri = new Uri('http://:pass@example.com');
        $this->assertEquals('', $uri->get('user_info'));
        
        $uri = new Uri('http://user:pass@example.com');
        $this->assertEquals('user:pass', $uri->get('user_info'));
        
        $uri = new Uri('user:pass@example.com');
        $this->assertEquals('user:pass', $uri->get('user_info'));
    }
    
    function testHost()
    {    
        $uri = new Uri('example.com');        
        $this->assertEquals('example.com', $uri->get('host'));

        $uri = new Uri('example.com:80');
        $this->assertEquals('example.com', $uri->get('host'));
        
        $uri = new Uri('http://example.com');
        $this->assertEquals('example.com', $uri->get('host'));
        
        $uri = new Uri('example.com:443');
        $this->assertEquals('example.com', $uri->get('host'));
        
        $uri = new Uri('http://example.com:443');
        $this->assertEquals('example.com', $uri->get('host'));
    }
    
    function testPort()
    {
        $uri = new Uri('example.com');
        $this->assertEquals(null, $uri->get('port'));

        $uri = new Uri('example.com:80');
        $this->assertEquals(null, $uri->get('port'));
        
        $uri = new Uri('http://example.com');
        $this->assertEquals(null, $uri->get('port'));
        
        $uri = new Uri('example.com:443');
        $this->assertEquals(null, $uri->get('port'));
        
        $uri = new Uri('http://example.com:443');
        $this->assertEquals(443, $uri->get('port'));
        
        $uri = new Uri('https://example.com:80');
        $this->assertEquals(80, $uri->get('port'));
    }
    
    function testPath()
    {
        $uri = new Uri('example.com');
        $this->assertEquals('', $uri->get('path'));

        $uri = new Uri('example.com:80/');
        $this->assertEquals('/', $uri->get('path'));
        
        $uri = new Uri('http://example.com/hello/world');
        $this->assertEquals('/hello/world', $uri->get('path'));

        $uri = new Uri('http://example.com/hello/world/');
        $this->assertEquals('/hello/world/', $uri->get('path'));
    }
    
    function testQuery()
    {
        $uri = new Uri('example.com?');
        $this->assertEquals('', $uri->get('query'));
        $uri = new Uri('example.com?hello_world');
        $this->assertEquals('hello_world', $uri->get('query'));
        $uri = new Uri('example.com?hello=world&go=google');
        $this->assertEquals('hello=world&go=google', $uri->get('query'));        
    }

    function testFragment()
    {
        $uri = new Uri('example.com?#');
        $this->assertEquals('', $uri->get('fragment'));
        $uri = new Uri('example.com?test=again&#hello-world');
        $this->assertEquals('hello-world', $uri->get('fragment'));
    }

    function testParsing()
    {
        $uri = new Uri("https://user:password@sub.host.com:8080/path/to/some/place/?hello=world#one");

        $this->assertEquals('https', $uri->get('scheme'));
        $this->assertEquals('user:password', $uri->get('user_info'));
        $this->assertEquals('sub.host.com', $uri->get('host'));
        $this->assertEquals(8080, $uri->get('port'));
        $this->assertEquals('/path/to/some/place/', $uri->get('path'));
        $this->assertEquals('hello=world', $uri->get('query'));
        $this->assertEquals('one', $uri->get('fragment'));
        $this->assertEquals('user:password@sub.host.com:8080', $uri->get('authority'));
        
        $uri = new Uri("https://user:password@sub.host.com:443/path/to/some/place/?hello=world#one");
        $this->assertNull($uri->get('port'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWhenUriIsInvalid()
    {
        new Uri(':');
    }

}
