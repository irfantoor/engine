<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Exception;
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

    function __construct($init = [])
	{
        parent::__construct($init);
	}

    # used by set($id, $value)
    public function setItem($id, $value = null)
    {
        if (!is_array($value))
            $value = [$value];

        parent::setItem(strtolower($id), ['id' => $id, 'value' => $value]);
    }

    public function add($id, $value)
    {
        $old = $this->get($id);
        if (!is_array($old))
            $old = [$old];

        $new = is_array($value) ? $value : [$value];
        $this->set($id, array_merge($old, array_values($new)));
    }

    public function has($id)
    {
        return parent::has(strtolower($id));
    }

    public function get($id, $default = [])
    {
        return $this->has($id) ? parent::get(strtolower($id))['value'] : $default;
    }

    public function getLine($id, $default = '')
    {
        $values = $this->get($id, []);
        $line = implode(', ', $values);
        return ('' !== $line) ? $line : $default;
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
