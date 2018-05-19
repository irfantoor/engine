<?php

namespace IrfanTOOR\Engine\Command;

use IrfanTOOR\Console\Command;
use IrfanTOOR\Console\ShellCommand;
use IrfanTOOR\Engine\Command\Model;
use IrfanTOOR\Engine\Filesystem;

class IE extends Command
{
    function __construct()
    {
        $this->help = [
            'Irfan\'s Engine console',
        ];
        
        $fs = new Filesystem(ROOT);

        $list = $fs->listContents('vendor/irfantoor/engine/src/Command/');
        foreach($list as $item) {
            $name = $item['basename'];
            if (strpos($name, '.php') !== false && $name != 'IE.php') {
                $name = ucfirst(str_replace('.php', '', $name));
                $cname = '\\IrfanTOOR\\Engine\\Command\\' . ucfirst($name);
                $class = new $cname;
                foreach($class->getHelp() as $cmd => $hlp) {
                    $this->help[strtolower($name)] = $hlp;
                    break;
                }
            }
        }
        
        $list = $fs->listContents('app/command/');
        foreach($list as $item) {
            $name = $item['basename'];
            if (strpos($name, '.php') !== false && $name != 'IE.php') {
                $name = ucfirst(str_replace('.php', '', $name));
                $cname = '\\App\\Command\\' . ucfirst($name);
                $class = new $cname;
                foreach($class->getHelp() as $cmd => $hlp) {
                    $this->help[strtolower($name)] = $hlp;
                    break;
                }
            }
        }
        
        parent::__construct();
    }

    function run()
    {
        $args = $this->args;
        array_shift($args);
        $command =  isset($args[0]) ? array_shift($args) : null;
        
        $method = null;
        if (strpos($command, ':') !== false) {
            list($command, $method) = explode(':', $command);
            $method = $command . '_' . $method;
        }
        
        if (
            $command 
            && (array_key_exists($command, $this->help))
            && $command !== 'help'
        ) {
            if (file_exists(ROOT . 'app/command/' . ucfirst($command) . '.php')) {
                $cname = '\\App\\Command\\' . ucfirst($command);
            } else {
                $cname = '\\IrfanTOOR\\Engine\\Command\\' . ucfirst($command);
            }
            $class = new $cname();
                $class->run();
        } else {
            if ($command === 'help') {
                $cmd = isset($args[0]) ? $args[0] : null;
            } else {
                $cmd = null;
            }
            
            if ($cmd) {
                if (strpos($cmd, ':') !== false) {
                    list($cmd, $method) = explode(':', $cmd);
                }
                
                if ($cmd && (array_key_exists($cmd, $this->help))) {
                    if ($cmd === 'help') {
                        parent::help(['help']);
                        exit;
                    }
                    if (file_exists(ROOT . 'app/command/' . ucfirst($cmd) . '.php')) {
                        $cname = '\\App\\Command\\' . ucfirst($cmd);
                    } else {
                        $cname = '\\IrfanTOOR\\Engine\\Command\\' . ucfirst($cmd);
                    }
                    
                    $class = new $cname();
                    $class->help([$cmd . ':' . $method]);
                }
            } else {
                parent::help($command);
            }
        }
    }
}
