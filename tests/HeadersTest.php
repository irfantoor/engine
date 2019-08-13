<?php

use IrfanTOOR\Test;

use IrfanTOOR\Engine\Http\Headers;

class HeadersTest extends Test
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
        $this->assertInstanceOf(Headers::class, $h);
        // $this->assertInstanceOf(Collection::class, $h);
    }

    function testCreateFromEnvironment()
    {
        $h = Headers::createFromEnvironment();

        $this->assertTrue(count($h->toArray()) > 4);
        $this->assertTrue($h->has('Host'));
        $this->assertTrue($h->has('Accept'));
        $this->assertTrue($h->has('Accept-Language'));
        $this->assertTrue($h->has('Accept-Charset'));
        $this->assertTrue($h->has('User-Agent'));
    }

    function testHas()
    {
        $h = $this->getHeaders();

        $this->assertTrue($h->has('content-length'));
        $this->assertTrue($h->has('content-type'));
        $this->assertFalse($h->has('Host'));
        $this->assertFalse($h->has('Accept'));
    }

    function testGet()
    {
        $h = $this->getHeaders();

        $this->assertEquals([100], $h->get('content-length'));
        $this->assertEquals(['text/html'], $h->get('content-type'));
        $this->assertNull($h->get('Host'));
        $this->assertEquals('World!', $h->get('Hello', 'World!'));
    }

    function testSet()
    {
        $h = $this->getHeaders();

        $h->set('Content-Length', 99);
        $this->assertEquals([99], $h->get('content-length'));

        $h->set('Content-Type', 'text/plain');
        $this->assertEquals(['text/plain'], $h->get('content-type'));

        $h->set('Host', 'irfantoor.com');
        $this->assertTrue($h->has('Host'));
        $this->assertNotNull($h->get('Host'));
        $this->assertEquals(['irfantoor.com'], $h->get('Host'));
    }

    function testSetMultiple()
    {
        $h = $this->getHeaders();

        $this->assertFalse($h->has('host'));
        $this->assertFalse($h->has('accept'));

        $h->setMultiple([
            'Host'   => 'irfantoor.com',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        ]);

        $this->assertTrue($h->has('host'));
        $this->assertTrue($h->has('accept'));
        $this->assertEquals(['irfantoor.com'], $h->get('host'));
        $this->assertEquals(
            ['text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'], 
            $h->get('accept')
        );
    }

    function testAdd()
    {
        $h = $this->getHeaders();

        # non existant entry
        $h->add('User-Agent', 'Alfa-Romeo v0.1');
        $this->assertEquals(['Alfa-Romeo v0.1'], $h->get('user-agent'));

        # existant entry
        $h->add('content-type', 'utf-8');
        $this->assertEquals(['text/html', 'utf-8'], $h->get('content-type'));
    }

    function testRemove()
    {
        $h = $this->getHeaders();

        $this->assertTrue($h->has('content-length'));
        $this->assertFalse($h->has('Host'));

        # non existant
        $h->remove('Host');
        $this->assertFalse($h->has('Host'));

        # existant
        $h->remove('content-length');
        $this->assertFalse($h->has('content-length'));
    }

    function testGetName()
    {
        $h = $this->getHeaders();
        $this->assertEquals('Content-Length', $h->getName('content-length'));
        $this->assertEquals('Content-Length', $h->getName('content-lenGth'));

        $h->set('Content-length', 99);
        $this->assertEquals('Content-length', $h->getName('Content-Length'));

        $this->assertEquals('Accept', $h->getName('Accept'));
    }

    function testGetLine()
    {
        $h = $this->getHeaders();
        $this->assertEquals('Content-Length: 100', $h->getLine('content-length'));

        $h->add('Content-Type', 'utf-8');
        $this->assertEquals('Content-Type: text/html, utf-8', $h->getLine('Content-Type'));

        $this->assertEquals('Accept: ', $h->getLine('Accept'));
        $this->assertEquals("Engine: Irfan's Engine", $h->getLine('Engine', "Irfan's Engine"));
    }

    function testToArray()
    {
        $h = new Headers();
        $this->assertArray($h->toArray());

        $h = Headers::createFromEnvironment();
        $ha = $h->toArray();

        foreach ($ha as $k => $v) {
            $this->assertEquals($v, $h->get($k));
        }
    }

    function testKeys()
    {
        $h = Headers::createFromEnvironment();
        $ha = $h->toArray();
        $keys = $h->keys();

        foreach ($keys as $k) {
            $this->assertEquals($ha[$k], $h->get($k));
        }
    }

    function testCaseInsensitive()
    {
        $h = $this->getHeaders();

        $this->assertTrue($h->has('content-length'));
        $this->assertEquals([100], $h->get('contenT-Length'));
        $this->assertTrue($h->has('CONTENT-TYPE'));
        $this->assertEquals(['text/html'], $h->get('CONTENT-type'));
    }

    function testRetainsLastSetKey()
    {
        $init = [
            'Content-Length' => 100,
            'Content-Type'   => 'text/html',
            'go'             => 'google',
        ];

        $expected = [
            'Content-Length' => [100],
            'Content-Type'   => ['text/html'],
            'GO'             => ['Google'],
        ];

        $h = new Headers($init);

        $this->assertEquals('go: google', $h->getLine('Go'));

        # Set the key value pair, which is accessible
        $h->set('GO', 'Google');
        $this->assertEquals('GO: Google', $h->getLine('Go'));
        $this->assertEquals($expected, $h->toArray());

        $h->add('Content-type', 'char: utf8');
        $this->assertEquals('Content-type: text/html, char: utf8', $h->getLine('content-type'));
    }

    function testSend()
    {
        $h = Headers::createFromEnvironment();
        $hh = $h->send();
        
        $ha = $h->keys();

        foreach ($ha as $k) {
            $header = array_shift($hh);
            $this->assertEquals($header, $h->getLine($k));
        }
    }
}
