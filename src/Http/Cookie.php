<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;

class Cookie
{
    protected $value    = [];
    protected $domain   = null;
    protected $path     = null;
    protected $expires  = null;
    protected $secure   = false;
    protected $httponly = false;

    public static function createFromArray(array $data, $options=[])
    {
        $cookies = [];
        foreach($data as $k=>$v) {
            $cookies[] = new static(
                [$k => $v],
                ($options['domain']   ?: null),
                ($options['path']     ?: null),
                ($options['expires']  ?: null),
                ($options['secure']   ?: false),
                ($options['httponly'] ?: false)
            );
        }
        return $cookies;
    }

    function __construct(
        $value,
        $domain   = null,
        $path     = null,
        $expires  = null,
        $secure   = false,
        $httponly = false
    ) {
        $this->value    = $value;
        $this->domain   = $domain ?: $_SERVER['HTTP_HOST'];
        $this->path     = $path ?: '/';
        $this->expires  = $expires ?: time()+24*60*60;
        $this->secure   = $secure;
        $this->httponly = $httponly;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getOptions()
    {
        return [
            'domain'   => $this->domain,
            'path'     => $this->path,
            'expires'  => $this->expires,
            'secure'   => $this->secure,
            'httponly' => $this->httponly,
        ];
    }

    private function _with($name, $value)
    {
        if ($value === $this->$name)
            return $this;

        $clone = clone $this;
        $clone->$name = $value;
        return $clone;
    }

    public function withValue($value)
    {
        return $this->_with('value', $value);
    }

    public function withDomain($domain)
    {
        return $this->_with('domain', $domain);
    }

    public function withPath($path)
    {
        return $this->_with('path', $path);
    }

    public function withExpires($expires)
    {
        return $this->_with('expires', $expires);
    }

    public function withSecure($secure)
    {
        return $this->_with('secure', $secure);
    }

    public function withHttpOnly($httponly)
    {
        return $this->_with('httponly', $httponly);
    }

    public function withOptions($options)
    {
        $current = $this->getOptions();
        $clone = $this;

        foreach ($options as $k => $v) {
            if (in_array($k, array_keys($current)) &&
                $v != $this->$k
            ) {
                $clone = $clone->_with($k, $v);
            }
        }

        return $clone;
    }

    public function send()
    {
        foreach($this->value as $k => $v) {
            setcookie($k, $v, $this->expires, $this->path, $this->domain, $this->secure, $this->httponly);
        }
    }
}
