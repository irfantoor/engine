<?php

namespace IrfanTOOR\Engine\Http;

class Headers
{
    /**
     * Special HTTP headers that do not have the "HTTP_" prefix
     *
     * @var array
     */
    protected static $special = [
        'CONTENT_TYPE' => 1,
        'CONTENT_LENGTH' => 1,
        'PHP_AUTH_USER' => 1,
        'PHP_AUTH_PW' => 1,
        'PHP_AUTH_DIGEST' => 1,
        'AUTH_TYPE' => 1,
    ];

    protected $data = [];

    /**
     * Create Headers from Enviroment class or an array of $_SERVER
     *
     * @param Environment|array $env
     *
     * @return Headers Collection
     */
    static function createFromEnvironment($env = [])
    {
        if (!($env instanceof Environment)) {
            $env = new Environment($env);
        }

        // Headers from environment
        $data = [];

        foreach($env as $k => $v) {
            $k = strtoupper($k);
            if (strpos($k, 'HTTP_') === 0) {
                $k = substr($k, 5);
            } else {
                if (!isset(static::$special[$k]))
                    continue;
            }

            // normalize key
            $k = str_replace(
                ' ',
                '-',
                ucwords(strtolower(str_replace('_', ' ', $k)))
            );

            $data[$k] = $v;
        }

        return new static($data);
    }

    function __construct($init = [])
	{
        $this->setMultiple($init);
	}

    function has($k) {
        $l = strtolower($k);
        return array_key_exists($l, $this->data);
    }

    function get($k, $default = null)
    {
        $l = strtolower($k);

        return 
            array_key_exists($l, $this->data) 
                ? $this->data[$l][1]
                : $default;
    }

    function set($k, $v)
    {
        if (!is_array($v)) {
            $v = [$v];
        }

        $l = strtolower($k);
        $this->data[$l] = [$k, $v];
    }

    function setMultiple($h)
    {
        foreach ($h as $k => $v) {
            $this->set($k, $v);
        }
    }

    function add($k, $v)
    {
        $o = $this->get($k);

        if ($o) {
            $v = array_merge($o, [$v]);
        }

        $this->set($k, $v);
    }

    function remove($k)
    {
        unset($this->data[strtolower($k)]);
    }

    function getName($k)
    {
        $l = strtolower($k);

        return 
            array_key_exists($l, $this->data) 
                ? $this->data[$l][0]
                : $k;
    }

    function getLine($k, $default = '')
    {
        $values = $this->get($k, []);
        $line = implode(', ', $values);
        $line = ('' !== $line) ? $line : $default;

        if ($this->has($k)) {
            return $this->getName($k) . ': ' . $line;
        } else {
            return $k . ': ' . $line;
        }
    }

    function toArray()
    {
        $h = [];

        foreach ($this->data as $k => $v) {
            $h[$v[0]] = $v[1];
        }

        return $h;
    }

    function keys() {
        return array_keys($this->toArray());
    }

    function send()
    {
        $headers = [];

        foreach ($this->toArray() as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $value) {
                    $headers[] = sprintf('%s: %s', $k, $value);
                }
            } else {
                $headers[] = sprintf('%s: %s', $k, $v);
            }
        }

        if (headers_sent())
            return $headers; # test -vv

        # send headers
        foreach ($headers as $header) {
            header($header, false);
        }

        return $headers; # test -v
    }
}
