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

/**
 * Cookie
 */
class Cookie extends Collection
{
    protected $received = [];

    protected $defaults = [
        'value' => '',
        'domain' => null,
        'hostonly' => null,
        'path' => null,
        'expires' => null,
        'secure' => false,
        'httponly' => false
    ];

    function __construct($cookie=[])
    {
        $this->received = $cookie;

        parent::__construct($this->defaults);
        $this->set('expires', time()+24*60*60);
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

    public function get($name, $default = null)
    {
        return isset($this->received[$name]) ? $this->received[$name] : $default;
    }

    public function _setItem($id, $value)
    {
        if ($this->has($k)) {
            if ($k == 'value') {
                $new = parent::get('value', []);
                foreach($value as $kk=>$vv) {
                    $new[$kk] = $vv;
                }
                parent::_setItem($k, $new);
            } else {
                parent::_setItem($k, $value);
            }
        } else {
            $new = parent::get('value', []);
            foreach($value as $kk=>$vv) {
                $new[$kk] = $vv;
            }
            parent::_setItem('value', $new);
        }
    }

    function send() {
        extract($this->toArray());
        foreach($value as $k => $v) {
             setcookie($k, $v, $expires, $path, $domain, $secure, $httponly);
        }
    }
}
