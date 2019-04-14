<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;
use Exception;

class Environment extends Collection
{
    function __construct($init = [])
    {
        # todo -- verify $_ENV, getenv()
        
        if (!is_array($init))
            throw new Exception('to be mocked $init must be an array');

        # from slim framework
        if ((isset($init['HTTPS']) && $init['HTTPS'] !== 'off') ||
            ((isset($init['REQUEST_SCHEME']) && $init['REQUEST_SCHEME'] === 'https'))) {
            $defscheme = 'https';
            $defport = 443;
        } else {
            $defscheme = 'http';
            $defport = 80;
        }

        $env = array_merge(
            [
                'SERVER_PROTOCOL'      => 'HTTP/1.1',
                'REQUEST_METHOD'       => 'GET',
                'REQUEST_SCHEME'       => $defscheme,
                'SCRIPT_NAME'          => '',
                'REQUEST_URI'          => '',
                'QUERY_STRING'         => '',
                'SERVER_NAME'          => 'localhost',
                'SERVER_PORT'          => $defport,
                'HTTP_HOST'            => 'localhost',
                'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
                'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
                'HTTP_USER_AGENT'      => 'Irfan\'s Engine',
                'REMOTE_ADDR'          => '127.0.0.1',
                'REQUEST_TIME'         => time(),
                'REQUEST_TIME_FLOAT'   => microtime(true),
            ],
            $_SERVER,
            $init
        );

        parent::__construct($env);
        $this->lock();
    }
}
