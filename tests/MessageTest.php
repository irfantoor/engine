<?php

use IrfanTOOR\Engine\Http\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    function testMessageInstance()
    {
        $m = new Message();
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Message::class, $m);
    }
    
    function testDefaultValues()
    {
        $m = new Message();
        $this->assertEquals('1.1', $m->getProtocolVersion());
        $this->assertEquals([], $m->getHeaders());
        $this->assertInstanceOf(IrfanTOOR\Engine\Http\Stream::class, $m->getBody());
        $this->assertEquals('', $m->getBody());        
    }

    function testGetHeaders()
    {
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
        $this->assertEquals(['unknown'], $m->getHeader('content-type', 'unknown'));
        $this->assertEquals(['unknown'], $m->getHeader('content-type', ['unknown']));
        $this->assertEquals([], $m->getHeader('content-type', null));
        
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

    function testWithHeaders()
    {
        $m = new Message();
        $m2 = $m->withHeader('Content-Type', 'plain/text');
        $this->assertEquals(['plain/text'], $m2->getHeader('content-type'));
        $this->assertEquals(['plain/text'], $m2->getHeader('content-type', 'unknown'));
        
        $m3 = $m2->withHeader('Content-Type', 'utf8');
        $this->assertEquals(['utf8'], $m3->getHeader('content-type'));
        $this->assertEquals(['plain/text'], $m2->getHeader('content-type'));
        $this->assertEquals([], $m->getHeader('content-type'));
    }
    
    function testAddHeader()
    {
        $m = new Message();
        $m2 = $m->withAddedHeader('Content-Type', 'plain/text');
        $this->assertEquals(['plain/text'], $m2->getHeader('content-type'));
        $m3 = $m2->withAddedHeader('Content-Type', 'utf8');
        $this->assertEquals(['plain/text', 'utf8'], $m3->getHeader('content-type'));
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
        
        $m2 = $m->withoutHeader('Content-Type');
        $this->assertEquals([], $m2->getHeader('content-type'));
        $this->assertEquals(['plain/text'], $m->getHeader('content-type'));            
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
        
        $m2 = $m->withAddedHeader('Content-Type', 'Charset: utf8');
        $this->assertEquals('Content-Type: plain/text, Charset: utf8', $m2->getHeaderLine('content-type'));   
    }
}
