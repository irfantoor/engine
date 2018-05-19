<?php

namespace IrfanTOOR\Engine\Command;

use IrfanTOOR\Console\Command;
use IrfanTOOR\Engine\Filesystem;

class About extends Command
{
    function __construct()
    {
        $this->help = [
            'About Irfan\'s Engine',
        ];
        
        $this->fs = new Filesystem(ROOT);
        parent::__construct();
    }

    function about()
    {
        $path = dirname(__DIR__) . '/';
        if (strpos($path, ROOT) === 0) {
            $path = substr($path, strlen(ROOT));
        }
        
        $file = dirname($path) . '/README.md';
        
        $contents = $this->fs->read($file);
        $contents = substr($contents, 0, strpos($contents, '#'));
        $this->writeln($contents, 'green');
    }
}
