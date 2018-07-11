<?php

use IrfanTOOR\Engine\Http\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    function testMessageInstance()
    {
        $m = new Message();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Message::class, $m);
        $this->assertInstanceOf(IrfanTOOR\Collection::class, $m);
    }
    
    function testDefaultValues()
    {
        $m = new Message();
        $this->assertEquals('1.1', $m->get('version'));
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Stream::class, $m->get('body'));
        $this->assertEquals('', $m->get('body'));
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Headers::class, $m->get('headers'));
        $this->assertEquals([], $m->getHeaders());
    }

    function testGetHeaders()
    {
        $m = new Message();
        $this->assertEquals([], $m->getHeaders());
        
        $h = [
            'content-type' => 'plain/text',
            'engine'       => 'ie',
        ];
        
        $m = new Message(['headers' => $h]);
        $this->assertEquals(
            [
                'content-type' => ['plain/text'],
                'engine'       => ['ie'],
            ], 
            $m->getHeaders()
        );
    }
    
    function testGetHeader()
    {
        $m = new Message();
        $this->assertEquals([], $m->getHeader('content-type'));
        $this->assertEquals('unknown', $m->getHeader('content-type', 'unknown'));
        $this->assertNull($m->getHeader('content-type', null));
        
        $m = new Message(
            [
                'headers' => [
                    'content-type' => 'plain/text',
                ]
            ]
        );
        
        $this->assertEquals(['plain/text'], $m->getHeader('content-type'));
        $this->assertEquals(['plain/text'], $m->getHeader('content-type', 'unknown'));
    }

    function tesSetHeaders()
    {
        $m = new Message();
        $m->setHeader('Content-Type', 'plain/text');
        $this->assertEquals(['plain/text'], $m->getHeader('content-type'));
        $this->assertEquals(['plain/text'], $m->getHeader('content-type', 'unknown'));
        
        $m->setHeader('Content-Type', 'utf8');
        $this->assertEquals(['utf8'], $m->getHeader('content-type'));
    }
    
    function testAddHeader()
    {
        $m = new Message();
        $m->addHeader('Content-Type', 'plain/text');
        $this->assertEquals(['plain/text'], $m->getHeader('content-type'));
        $m->addHeader('Content-Type', 'utf8');
        $this->assertEquals(['plain/text', 'utf8'], $m->getHeader('content-type'));
    }
    
    function testRemoveHeader()
    {
        $m = new Message(
            [
                'headers' => [
                    'content-type' => 'plain/text',
                ]
            ]
        );    
        
        $m->removeHeader('Content-Type');
        $this->assertEquals([], $m->getHeader('content-type'));            
    }
    
    function testGetHeaderLine()
    {
        $m = new Message(
            [
                'headers' => [
                    'Content-type' => 'plain/text',
                ]
            ]
        );    
        
        $this->assertEquals('Content-type: plain/text', $m->getHeaderLine('content-type'));
        
        $m->addHeader('Content-Type', 'Charset: utf8');
        $this->assertEquals('Content-Type: plain/text, Charset: utf8', $m->getHeaderLine('content-type'));   
    }
}
