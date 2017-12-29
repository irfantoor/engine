<?php
/**
 * IrfanTOOR\Engine
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/collection/blob/master/LICENSE (MIT License)
 */

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Debug;

Class Uri extends Collection
{
    protected static $default_ports = [
        ''      => 80,
        'http'  => 80,
        'https' => 443,
    ];

    protected static $regex = [
        'userinfo'  =>  '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=]+|%(?![A-Fa-f0-9]{2}))/u',
        'path'      => '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
        'query'     => '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
    ];

    protected static $defaults = [
        'scheme'    => '',
        'user'      => '',
        'pass'      => '',
        'host'      => '',
        'port'      => null,
        'path'      => '',
        'query'     => '',
        'fragment'  => '',

        'userinfo'  => '',
        'authority' => '',
        'basepath'  => '',
    ];

    function __construct($url = null)
    {
        parent::__construct(self::$defaults);
        if ($url) {
            if (!is_string($url) && !method_exists($url, '__toString')) {
                throw new \InvalidArgumentException('Uri must be a string');
            }

            $this->set(parse_url($url));
            $this->_process();
        }
    }

    /**
     * replaces the contents of a key with a value in a cloned version if the
     * need be.
     *
     * @param string $key
     * @param array  $args
     *
     * @return Uri Either this instance if not changed or a clone with the
     *             requested change
     */
    public function with($key, $value)
    {
        if ($key == 'userinfo')
        {
            if (!is_array($value) && count($value)!=2)
                throw new \Exception("userinfo requires an array with exactly 2 parameters", 1);

            if ($value[0] === $this->get('user') && $value[1] === $this->get('pass'))
                return $this;
        } else {
            if ($value === $this->get($key))
                return $this;
        }

        $clone = clone $this;

        if ($key == 'userinfo') {
            $clone->set('user', $value[0]);
            $clone->set('pass', $value[1]);
        } else {
            $clone->set($key, $value);
        }

        $clone->_process();
        return $clone;
    }

    /*
     * Process the contents after a call of type withXxxx e.g. withPort(8080)
     */
    private function _process() {
        # Extract
        extract ($uri = $this->toArray());

        # Validate/normalize the data
        foreach($uri as $k => $v) {
            switch($k) {
                case 'scheme':
                    if (!is_string($scheme) && !method_exists($scheme, '__toString')) {
                        throw new InvalidArgumentException('scheme must be a string');
                    }

                    $scheme = str_replace('://', '', strtolower((string)$scheme));
                    if (!in_array($scheme, array_keys(self::$default_ports))) {
                        throw new \InvalidArgumentException('Not a valid scheme: ' . $scheme);
                    }
                    break;

                case 'port':
                    $port = ($port && (self::$default_ports[$scheme] != $port)) ? $port : null;
                    if (!is_null($port) && (!is_integer($port) || ($port < 1 || $port > 65535))) {
                        throw new \InvalidArgumentException('Uri port must be null or an integer between 1 and 65535 (inclusive)');
                    }
                    break;

                case 'fragment':
                    if (!is_string($fragment) && !method_exists($fragment, '__toString')) {
                        throw new \InvalidArgumentException('Uri fragment must be a string');
                    }
                    $fragment = ltrim((string)$fragment, '#');
                    break;

                case 'user':
                case 'pass':
                case 'path':
                case 'query':
                    $rindex = ($k=='user' || $k == 'pass') ? 'userinfo' : $k;
                    $r = self::$regex[$rindex];
                    $$k = preg_replace_callback(
                        $r,
                        function ($match) {
                            return rawurlencode($match[0]);
                        },
                        $$k
                    );
                    break;

                default:
                    # nothing to filter :)
            }
        }

        # Process
        $userinfo  = $user . ($pass ? ':' . $pass : '');
        $authority = ($userinfo ? $userinfo . '@' : '') . $host . ($port ? ':' . $port : '');
        $basepath  = rtrim(ltrim($path, '/'), '/');

        # Update
        foreach($uri as $k=>$v)
            $this->set($k, $$k);
    }

    public function __toString()
    {
        $url = '';
        extract($this->toArray());

        $url = $scheme ? $scheme . ':' : '';
        $url .= $authority ? ($scheme ? '//' : '') . $authority : '';

        if ($authority && ($path['0'] !== '/'))
             $path = '/' . $path;

        if (!$authority && ($path['0'] === '/'))
            $path = '/' . ltrim($path, '/');

        $url .= $path;
        $url .= $query ? '?' . $query : '';
        $url .= $fragment ? '#' . $fragment : '';

        return $url;
    }

    static function createFromEnvironment($env = [])
    {
        if (!($env instanceof Environment)) {
            $env = new Environment($env);
        }

        $host = $env['HTTP_HOST'] ?: ($env['SERVER_NAME'] ?: 'localhost');
        $protocol = $env['SERVER_PROTOCOL'] ?: 'HTTP/1.1';
        $pos = strpos($protocol, '/');
        $ver = substr($protocol, $pos + 1);
        $url = ($env['REQUEST_SCHEME'] ?: 'http') . '://' . $host . ($env['REQUEST_URI'] ?: '/');

        return self::createFromString($url);
    }

    static function createFromString($url)
    {
        return new static($url);
    }
}
