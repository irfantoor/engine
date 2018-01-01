<?php
/**
 * IrfanTOOR\Smart
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/engine/blob/master/LICENSE (MIT License)
 */

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;

class Cookie extends Collection
{
    protected $defaults = [
        'value' => [],
        'domain' => null,
        'hostonly' => null,
        'path' => null,
        'expires' => null,
        'secure' => false,
        'httponly' => false
    ];

    function __construct($cookie=[])
    {
        parent::__construct($this->defaults);
        $this->set('expires', time()+24*60*60);
        $this->set($cookie);
    }

    /**
     * Set default cookie properties
     *
     * @param array $settings
     */
    public function setDefaults(array $settings)
    {
        $this->defaults = array_replace($this->defaults, $settings);
    }

    public function get($id, $default = null)
    {
        if ($this->has($id))
            return parent::get($id);

        if ($this->has('value.' . $id))
            return parent::get('value.' . $id);

        return $default;
    }

    # used by set
    public function _setItem($id, $value)
    {
        if ($this->has($id))
            parent::set($id, $value);
        elseif ($this->has('value.' . $id)
            parent::set('value.' . $id, $value);
    }

    function send() {
        extract($this->toArray());
        foreach($value as $k => $v) {
             setcookie($k, $v, $expires, $path, $domain, $secure, $httponly);
        }
    }
}
