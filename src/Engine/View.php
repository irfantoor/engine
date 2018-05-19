<?php

namespace IrfanTOOR\Engine;

use Latte\Engine as Latte;

class View
{
    protected $controller;
    
    function __construct($controller)
    {
        $this->controller = $controller;
    }
    
    function __call($func, array $args)
    {
        try {
            $result = call_user_func_array([$this->controller, $func], $args);
            return $result;
        } catch(Exception $e) {
        }

        throw new Exception("Method: $func, does not exist!", 1);
    }
    
    function process($tplt, $data = [])
    {        
        $tplt = ROOT . 'app/view/' . $tplt . '.php';
        
        if (!is_file($tplt))
            throw new Exception("tplt: {$tplt}, not found");

        $latte = new Latte;
        
        $tmp_dir = $this->config('cache.tmp');
        $tmp_dir = $tmp_dir ? ROOT . $tmp_dir : '/tmp';
        
        $latte->setTempDirectory($tmp_dir);
        $result = $latte->renderToString($tplt, $data);
        return $result;
    }
}