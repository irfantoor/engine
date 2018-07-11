<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Http\Environment;

class Headers extends Collection
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

    /**
     * Create Headers from Enviroment class or an array of $_SERVER
     *
     * @param Environment|array $env
     *
     * @return Headers Collection
     */
    public static function createFromEnvironment($env = [])
    {
        if (!($env instanceof Environment)) {
            $env = new Environment($env);
        }

        // Headers from environment
        $data = [];
        foreach($env as $k=>$v) {
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

    public function __construct($init = [])
	{
        parent::__construct($init);
	}

    # used by set($id, $value)
    public function setItem($id, $value = null)
    {    
        if (!is_array($value)) {
            $value = [$value];
        }
        
        parent::setItem(strtolower($id), ['id' => $id, 'value' => $value]);
    }

    public function add($id, $value)
    {
        if ($this->has($id)) {
            $old = $this->get($id);
            $new = is_array($value) ? $value : [$value];
            $this->set($id, array_merge($old, array_values($new)));        
        } else {
            $this->set($id, $value);
        }        
    }

    public function has($id)
    {
        return parent::has(strtolower($id));
    }

    public function get($id, $default = [])
    {
        return $this->has($id) ? parent::get(strtolower($id))['value'] : $default;
    }

    public function getName($id)
    {
        return $this->has($id) ? parent::get(strtolower($id))['id'] : $id;
    }

    public function getLine($id, $default = '')
    {
        $values = $this->get($id, []);
        $line = implode(', ', $values);
        $line = ('' !== $line) ? $line : $default;
        
        if ($this->has($id)) {    
            return $this->getName($id) . ': ' . $line;
        } else {
            return $id . ': ' . $line;
        }
    }

    public function remove($id)
    {
        parent::remove(strtolower($id));
    }

    public function toArray()
    {
        $headers = [];
        foreach(parent::toArray() as $v) {
            $headers[$v['id']] = $v['value'];
        }
        return $headers;
    }

    function keys() {
        return array_keys($this->toArray());
    }

    function send()
    {
        if (headers_sent())
            return;

        foreach($this->toArray() as $k=>$v) {
            if (is_array($v)) {
                foreach ($v as $value) {
                    header(sprintf('%s: %s', $k, $value), false);
                }
            } else {
                header(sprintf('%s: %s', $k, $v), false);
            }
        }
    }
}
