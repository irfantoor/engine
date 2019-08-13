<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;

/**
 * Cookie to manage the Request, ServerRequest or Response cookies
 */
class Cookie extends Collection
{
    /**
     * Constructs a cookie from provided key, value pair(s) and options
     */
    function __construct($init = [])
    {
        extract([
            'name'     => 'undefined',
            'value'    => null,
            'expires'  => time() + 24 * 60 * 60,
            'path'     => '/',
            'domain'   => isset($_SERVER['SEVRER_NAME']) ? $_SERVER['SEVRER_NAME'] : 'localhost',
            'secure'   => false,
            'httponly' => false
        ]);

        extract($init, EXTR_IF_EXISTS);

        parent::__construct([
            'name'     => $name,
            'value'    => $value,
            'expires'  => $value == null ? 1: $expires,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
        ]);
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
