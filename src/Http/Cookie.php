<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;

/**
 * Cookie to manage the Request, ServerRequest or Response cookies
 */
class Cookie extends Collection
{
    public static function createFromArray($data = [])
    {
        $cookies = [];
        foreach($data as $k=>$v) {
            $cookies[] = new static([
                'name' => $k,
                'value' => $v,
            ]);
        }
        
        return $cookies;
    }
    
    /**
     * Constructs a cookie from provided key, value pair(s) and options
     */
    function __construct($init = [])
    {
        $env = new Environment;
        
        extract([
            'name'     => 'undefined',
            'value'    => null,
            'expires'  => time() + 24 * 60 * 60,
            'path'     => '/',
            'domain'   => $env['SEVRER_NAME'],
            'secure'   => false,
            'httponly' => false
        ]);
        
        extract($init, EXTR_IF_EXISTS);

        if ($value === null)
            $expires = 1;

        parent::__construct([
            'name'     => $name,
            'value'    => $value,
            'expires'  => $expires,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
        
        ]);
    }

    /**
     * Returns the default cookie manipulation options
     *
     * @return array
     */
    function options()
    {
        extract($this->toArray());
        
        return [
            'domain'   => $domain,
            'path'     => $path,
            'expires'  => $expires,
            'secure'   => $secure,
            'httponly' => $httponly,
        ];
    }

    /**
     * Sets the cookie to be sent by the headers
     */
    function send()
    {
        if (!headers_sent()) {
            extract($this->toArray());
            setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
        }
    }
}
