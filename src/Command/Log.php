<?php

namespace IrfanTOOR\Engine\Command;

use IrfanTOOR\Console\Command;

class Log extends Command
{
    function __construct()
    {
        
        $this->help = [
            'Log console',
            'log:config' => 'displays the cache config',
        ];
        
        parent::__construct();
    }

    function log_config()
    {
        $this->writeln(['log:config'], ['bg_blue','white']);
        $config = require APP . 'config.php';
        $this->_dump($config['log'] ?: [], false);
    }
}
