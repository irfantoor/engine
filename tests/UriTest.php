<?php

use IrfanTOOR\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    function getUri($url = null)
    {
        return new Uri($url);
    }

    function testUriInstance()
    {
        $u = $this->getUri();
        $this->assertInstanceOf('IrfanTOOR\Uri', $u);
        $this->assertInstanceOf('IrfanTOOR\Collection', $u);
    }

    function testDefault()
    {
        $u = $this->getUri();

        $expected = [
            'scheme'    => '',
            'user'      => '',
            'pass'      => '',
            'host'      => '',
            'port'      => '',
            'base_path' => '/',
            'path'      => '',
            'query'     => '',
            'fragment'  => '',
        ];

        $this->assertEquals($expected, $u->toArray());
    }

    function testParsing()
    {
        $u = $this->getUri("https://user:password@sub.host.com:8080/path/to/some/place/?hello=world#one");

        $expected = [
            'scheme'    => 'https',
            'user'      => 'user',
            'pass'      => 'password',
            'host'      => 'sub.host.com',
            'port'      => 8080,
            'base_path' => 'path/to/some/place',
            'path'      => '/path/to/some/place/',
            'query'     => 'hello=world',
            'fragment'  => 'one',
        ];

        $this->assertEquals($expected, $u->toArray());
    }
}
