<?php

namespace IrfanTOOR\Engine\Http;

use InvalidArgumentException;
use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Http\Environment;
use Psr\Http\Message\UriInterface;

/**
 * Value object representing a URI.
 *
 * This interface is meant to represent URIs according to RFC 3986 and to
 * provide methods for most common operations. Additional functionality for
 * working with URIs can be provided on top of the interface or externally.
 * Its primary use is for HTTP requests, but may also be used in other
 * contexts.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * Typically the Host header will be also be present in the request message.
 * For server-side requests, the scheme will typically be discoverable in the
 * server parameters.
 *
 * @link http://tools.ietf.org/html/rfc3986 (the URI specification)
 */
class Uri extends Collection
{

    protected static $default_ports = [
        # ''      => 80,
        'http'  => 80,
        'https' => 443,
    ];

//     protected static $regex = [
//         'userinfo'  =>  '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=]+|%(?![A-Fa-f0-9]{2}))/u',
//         'path'      => '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
//         'query'     => '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
//     ];
    
    function __construct($uri = null)
    {
        $env = new Environment();
        $host = $env['HTTP_HOST'] ?: ($env['SERVER_NAME'] ?: 'localhost');
        $host = explode(':', $host)[0];
        extract([
            'scheme'   => 'scheme',
            'user'     => null,
            'pass'     => null,
            'host'     => $host,
            'port'     => null,
            'path'     => '',
            'query'    => '',
            'fragment' => '',
            
            'user_info' => '',
            'authority' => '',
            'base_path' => '',
        ]);
        
        if (!$uri) {
            $uri =  'http://' . $host . ($env['REQUEST_URI'] ?: '/');
        } else {
            if (strpos($uri, '://') === false) {
                $uri = 'scheme://' . $uri;
            }
        }
        
        $parsed = parse_url($uri);
        if (!$parsed) {
            throw new \InvalidArgumentException('Invalid uri');
        }

        extract($parsed);
        
        $uri = [
            'scheme'   => $scheme,
            'user'     => $user,
            'pass'     => $pass,
            'host'     => $host,
            'port'     => $port,
            'path'     => $path,
            'query'    => $query,
            'fragment' => $fragment,
        ];
        
        foreach($uri as $k=>$v) {
            $this->setItem($k, $v);
        }
        
        $this->_process();
    }
    
    private function _process()
    {
        extract($this->toArray());
        $user_info = '';
        if ($user && $pass) {
            $user_info = $user . ':' . $pass;
        }
        $this->setItem('user_info', $user_info);
        
        if ($scheme === 'scheme')
            $scheme = '';
            
        if ($scheme === '') {
            if ($port === 443) {
                $scheme = 'https';
            } else {
                $scheme = 'http';
            }
            $this->setItem('scheme', $scheme);
        }
            
        if ($port) {
            $default_port = self::$default_ports[$scheme] ?: null;
            if ($default_port === $port) {
                $port = null;
            }
        }
        
        $this->setItem('port', $port);
        
        $authority = 
            ($user_info ?  $user_info . '@' : '') .
            $host . 
            (isset($port) ? ':' . $port : '');
        
        $this->setItem('authority', $authority);
        $this->setItem('basepath', rtrim(ltrim($path, '/'), '/'));
    }
    
    function set($k, $v = null)
    {
        parent::set($k, $v);
        $this->_process();
    }
    
    function __toString()
    {
        $url = '';
        extract($this->toArray());

        $url = $scheme ? $scheme . '://' : '';
        $url .= $authority;
        if ($authority && ($path['0'] !== '/'))
             $path = '/' . $path;

        if (!$authority && ($path['0'] === '/'))
            $path = '/' . ltrim($path, '/');

        $url .= $path;
        $url .= $query ? '?' . $query : '';
        $url .= $fragment ? '#' . $fragment : '';

        return $url;
    }    
}
