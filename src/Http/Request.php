<?php

namespace IrfanTOOR\Engine\Http;

use Psr\Http\Message\UriInterface;
use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Http\Message;

/**
 * Representation of an outgoing, client-side request.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - HTTP method
 * - URI
 * - Headers
 * - Message body
 *
 * During construction, implementations MUST attempt to set the Host header from
 * a provided URI if no Host header is provided.
 *
 * Requests are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
class Request extends Message
{   
    function __construct($init = [])
    {
        parent::__construct($init);
        extract([
            'method' => 'GET',
            'uri'    => '',
        ]);
        extract($init, EXTR_IF_EXISTS);

        $this->set([
            'method' => $method,
            'uri'    => new Uri($uri),
        ]);
    }
    
    function __clone()
    {
        parent::__clone();
        $this->set('uri', clone $this->get('uri'));
    }
    
    # todo -- send request
    # todo -- send - must return a response
    # todo -- sendAsync - must return a promise
    /*
     * Sends the request
     * 
     * returns IrfanTOOR\Engine\Http\Response
     */
    function send()
    {
        $uri = (string) $this->get('uri');
        if (!$uri)
            throw new \Exception('URI not defined');
            
        print_r($this);
    }
}
