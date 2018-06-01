<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Exception;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use IrfanTOOR\Engine\Http\Stream;

use IrfanTOOR\Debug;


class Message extends Collection
{
    function __construct($init = [])
    {
        # defaults
        extract([
            'version' => '1.1',
            'headers' => [],
            'body'    => '',
        ]);
        
        # todo -- verify the cyclic protection against the same var name
        # e.g. init in this case
        
        extract($init, EXTR_IF_EXISTS);
        
        $stream = new Stream();
        if ($body)
            $stream->write($body);
            
        parent::__construct([
            'version' => $version,
            'headers' => new Headers($headers),
            'body'    => $stream,
        ]);
    }
    
    function __clone()
    {
        $this->set('headers', clone $this->get('headers'));
    }    

    /**
     * Writes at the end of the stream (It does not clones the Message)
     * It is not part of the PSR Implementation
     *
     * @param string $contents
     */
    public function write($contents)
    {
        if (!is_string($contents)) {
            throw new Exception('$contents can only be of type string');
        }

        $stream = $this->get('body');
        $stream->write($contents);
    }
}
