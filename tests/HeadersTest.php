<?php

use IrfanTOOR\Engine\Http\Headers;
use PHPUnit\Framework\TestCase;

class HeadersTest extends TestCase
{
    function getHeaders()
    {
        return new Headers([
            'Content-Length' => 100,
            'Content-Type'   => 'text/html',
        ]);
    }

    function testHeadersInstance()
    {
        $h = $this->getHeaders();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Headers::class, $h);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $h);
    }

    function testCaseInsensitive()
    {
        $h = $this->getHeaders();

        $this->assertTrue($h->has('content-length'));
        $this->assertEquals(100, $h->get('contenT-Length'));

        $this->assertTrue($h->has('CONTENT-TYPE'));
        $this->assertEquals('text/html', $h->get('CONTENT-type'));
    }

    function testRetainsLastSetKey()
    {
        $init = [
            'Content-Length' => 100,
            'Content-Type'   => 'text/html',
            'go'             => 'google',
        ];

        $expected = [
            'Content-Length' => 100,
            'Content-Type'   => 'text/html',
            'GO'             => 'Google',
        ];

        $h = new Headers($init);

        $this->assertEquals('google', $h->get('Go'));
        $this->assertEquals($init, $h->toArray());

        # Set the key value pair, which is accessible
        $h->set('GO', 'Google');
        $this->assertEquals('Google', $h->get('Go'));
        $this->assertEquals($expected, $h->toArray());
    }
}
