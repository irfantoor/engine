<?php

namespace IrfanTOOR\Engine\Command;

use IrfanTOOR\Console\Command;
use IrfanTOOR\Engine\Filesystem;

class Cache extends Command
{
    protected $config;
    
    function __construct()
    {
        $file = ROOT . 'app/config.php';
        
        $config = file_exists($file) ? require $file : [];        
        $this->config = isset($config['cache']) ? $config['cache'] : [];
        
        $this->help = [
            'Cache console',
            'cache:config' => 'displays the cache config',
            'cache:clean'  => [
                'cleans the cached pages or views',
                '
FORMAT
------
cache:clean all|db|views

OPTIONS
-------
all      cleans the db and views cache
db       cleans the database cache of pages or posts etc.
views    cleans the template caches of views
                '
            ],
        ];
        
        parent::__construct();
    }

    function cache_config()
    {
        $config = require APP . 'config.php';
        $this->writeln(['cache:config'], ['bg_blue','white']);
        $this->_dump($this->config);
    }

    function cache_clean($args)
    {
        $option = isset($args[0]) ? $args[0] : null;
        switch($option) {
            case 'all':
            case 'db':
            case 'views':
                $action = '_clean' . ucfirst($option);
                $this->$action();
                break;

            default:
                $this->help('cache:clean');
                exit;
        }
    }

    private function _cleanAll()
    {
        $this->_cleanDb();
        $this->_cleanViews();
    }

    private function _cleanDb()
    {
        $this->writeln('[ ] todo -- Cache->cleanDb', 'yellow');
    }

    private function _cleanViews()
    {
        $path = $this->config['tmp'] ?: 'tmp/';        
        $fs = new Filesystem(ROOT);

        $passed = 0;
        $failed = 0;

        $list = $fs->listContents($path);

        foreach($list as $item) {
            try {
                if ($fs->delete($item['path'])) {
                    $passed ++;
                    $this->writeln('[x] ' . $item['basename'] . ' -- deleted', 'green');
                } else {
                    $this->writeln('[ ] ' . $item['basename'] . ' -- not deleted', 'red');
                }
                
            } catch (Exception $e) {
                $failed ++;
                $c->writeln('[ ] ' . $e->getMessage(), 'error');
            }
        }

        if ($passed) {
            $this->writeln('[x] files deleted: ' . $passed, 'green');
        } else {
            $this->writeln('[ ] files deleted: ' . $passed, 'red');
        }

        if ($failed) {
            $this->writeln('[ ] files could not process: ' . $failed, 'red');
        }
    }
}
