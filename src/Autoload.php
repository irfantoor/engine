<?php

namespace IrfanTOOR;

class Autoload
{
    protected static $map;

    static function map() {
        return self::$map;
    }

    static function register ()
    {
        self::$map = [
            'App\\' => 'app/'
        ];

        spl_autoload_register(function($class) {
            $aclass = explode('\\',$class);
    		$class = array_pop($aclass);
    		$ns = implode('\\', $aclass);
            if ($ns === 'IrfanTOOR') {
                $path = __DIR__ . '/';
            }
            else {
                foreach(Autoload::map() as $k=>$v) {
                    if (strpos($ns, $k) === 0) {
                        $ns = str_replace($k, $v, $ns);
                    }
                    $ns = str_replace('\\','/', $ns) . '/';
                    $path = ROOT . $ns;
                    break;
                }
            }

    		$file = $path . str_replace("_", "/", $class) . ".php";
    		if (file_exists($file))
    			require  $file;
            
    		return;
        });
    }
}

# Autoload::register();
