<?php
/**
 * IrfanTOOR\Smart
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/collection/blob/master/LICENSE (MIT License)
 */

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

    function __construct($init = [])
	{
        parent::__construct($init);
	}

    /**
     * Create new headers collection from the environment
     *
     * @param Environment $environment The Smart Environment
     *
     * @return self
     */
    public static function createFromEnvironment($env = [])
    {
        if (!($env instanceof Environment)) {
            $env = new Environment($env);
        }
        $data = [];
        foreach($env as $k=>$v) {
            $k = strtoupper($k);
            if (strpos($k, 'HTTP_') === 0)
                $k = substr($k, 5);
            elseif (!isset(static::$special[$k]))
                continue;

            $k = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", $k))));
            $data[$k] = $v;
        }

        return new static($data);
    }

    public function setItem($id, $value=null)
    {
        parent::setItem(strtolower($id), ['id' => $id, 'value'=>$value]);
    }

    public function add($id, $value)
    {
        $old = $this->get($id, []);
        $new = is_array($value) ? $value : [$value];
        $this->set($key, array_merge($old, array_values($new)));
    }

    public function has($id)
    {
        return parent::has(strtolower($id));
    }

    public function get($id, $default=null)
    {
        return $this->has($id) ? parent::get(strtolower($id))['value'] : $default;
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
