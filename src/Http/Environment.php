<?php
/**
 * IrfanTOOR\Smart
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/engine/blob/master/LICENSE (MIT License)
 */

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;

class Environment extends Collection
{
    function __construct($mock = [])
    {
        # todo --
        # if (!isset($_SESSION))
        #     session_start();

        # from slim framework
        if ((isset($mock['HTTPS']) && $mock['HTTPS'] !== 'off') ||
            ((isset($mock['REQUEST_SCHEME']) && $mock['REQUEST_SCHEME'] === 'https'))) {
            $defscheme = 'https';
            $defport = 443;
        } else {
            $defscheme = 'http';
            $defport = 80;
        }

        $data = array_merge(
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
            $mock
            # ['SESSION' => $_SESSION]
        );

        parent::__construct($data);
    }
}