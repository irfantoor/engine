<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;

/**
 * Cookie to manage the Request, ServerRequest or Response cookies
 */
class Cookie
{
    protected $name     = null;
    protected $value    = [];
    protected $domain   = null;
    protected $path     = null;
    protected $expires  = null;
    protected $secure   = false;
    protected $httponly = false;

    /**
     * Creates cookies from provided key, value pair(s) and options
     *
     * @param array $data
     * @param array $options
     *
     * @return array
     */
    public static function createFromArray(array $data, $options=[])
    {
        $cookies = [];
        foreach($data as $k=>$v) {
            $cookies[] = new static(
                $k,
                $v,
                ($options['expires']  ?: null),
                ($options['path']     ?: null),
                ($options['domain']   ?: null),
                ($options['secure']   ?: false),
                ($options['httponly'] ?: false)
            );
        }
        return $cookies;
    }

    /**
     * Constructs a cookie from provided key, value pair(s) and options
     *
     * @param string      $name
     * @param mixed       $value
     * @param null|int    $expires
     * @param null|string $path
     * @param null|string $domain
     * @param bool        $secure
     * @param bool        $httponly
     */
    function __construct(
        $name,
        $value    = null,
        $expires  = null,
        $path     = null,
        $domain   = null,
        $secure   = false,
        $httponly = false
    ) {
        if ($value === null)
            $expires = 1;

        $this->name     = $name;
        $this->value    = json_encode($value);
        $this->expires  = $expires ?: time()+24*60*60;
        $this->path     = $path ?: '/';
        $this->domain   = $domain ?: $_SERVER['HTTP_HOST'];
        $this->secure   = $secure;
        $this->httponly = $httponly;
    }

    /**
     * Returns the name value pair as array as [$name => $value]
     *
     * @return array
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the name value pair as array as [$name => $value]
     *
     * @return array
     */
    public function getValue()
    {
        return json_decode($this->value, 1);
    }

    /**
     * Returns the default cookie manipulation options
     *
     * @return array
     */
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

    /**
     * Private function to manipulate the with calls
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return Cookie
     */
    private function _with($name, $value)
    {
        if ($value === $this->$name)
            return $this;

        $clone = clone $this;
        $clone->$name = $value;
        return $clone;
    }

    /**
     * Return the clone of this cookie by changing the value provided
     *
     * @param mixed $value
     *
     * @return Cookie
     */
    public function withValue($value)
    {
        $cookie = $this->_with('value', json_encode($value));
        if ($value === null)
            $cookie = $cookie->withExpiry(1);
        return $cookie;
    }

    /**
     * Return the clone of this cookie by changing the expiry
     *
     * @param int $expires - time in seconds
     *
     * @return Cookie
     */
    public function withExpiry($expires)
    {
        return $this->_with('expires', $expires);
    }


    /**
     * Return the clone of this cookie by changing the domain value
     *
     * @param string $domain
     *
     * @return Cookie
     */
    public function withDomain($domain)
    {
        return $this->_with('domain', $domain);
    }

    /**
     * Return the clone of this cookie by changing the path value
     *
     * @param string $path
     *
     * @return Cookie
     */
    public function withPath($path)
    {
        return $this->_with('path', $path);
    }



    /**
     * Return the clone of this cookie to use only the secure connection
     *
     * @param bool $secure
     *
     * @return Cookie
     */
    public function withSecure($secure)
    {
        return $this->_with('secure', $secure);
    }

    /**
     * Return the clone of this cookie by changing the access by http request
     * only
     *
     * @param bool $httponly
     *
     * @return Cookie
     */
    public function withHttpOnly($httponly)
    {
        return $this->_with('httponly', $httponly);
    }

    /**
     * Return the clone of this cookie by changing the different options
     * provided as array
     *
     * @param array $options
     *
     * @return Cookie
     */
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

    public function toArray()
    {
        return array_merge(
            ['name'  => $this->getName()],
            ['value' => $this->getValue()],
            $this->getOptions()
        );
    }

    /**
     * Sets the cookie to be sent by the headers
     */
    public function send()
    {
        setcookie(
            $this->name,
            $this->getValue(),
            $this->expires,
            $this->path,
            $this->domain,
            $this->secure,
            $this->httponly
        );
    }
}
