<?php

namespace IrfanTOOR\Engine\Command;

use IrfanTOOR\Console\Command;
use IrfanTOOR\Console\ShellCommand;
use IrfanTOOR\Engine\Filesystem;

class Model extends Command
{
    protected $fs;
    
    function __construct()
    {
        $this->help = [
            'Model console',
            'model:create' => [
                'creates a model',
                '
Cretes a model

FORMAT
------
model:create modelname

OPTIONS
-------
modelname    name of the model

e.g. ./ie model:create posts
                '
            ],
            'model:info' => [
                'information of a specific model',
                '
Information of a specific model

FORMAT
------
model:info [modelname]

OPTIONS
-------
modelname    name of the model
                '
            ],
            'model:list' => 'list all the defined models',
        ];
        
        $this->fs = new Filesystem(ROOT);
        
        parent::__construct();
    }
    
    function model_create($args)
    {
        $class = isset($args[0]) ? $args[0] : null;
        if (!$class) {
            $this->help('model:create');
            exit;
        }
        $class = ucfirst($class);
        $file  = strtolower($class) . '.sqlite';
        
        # source path
        $path = dirname(__FILE__) . '/';
        if (strpos($path, ROOT) === 0) {
            $path = substr($path, strlen(ROOT));
        }
        
        # create the model file
        if ($this->fs->has('app/Model/' . $class . '.php')) {
            $this->writeln('[ ] model : ' . $class . ' -- already exists', 'yellow');
        } else {
            $contents = $this->fs->read($path . 'app/Model/Model.php.src');
            $contents = str_replace('{$class}', $class, $contents);
            $contents = str_replace('{$file}', $file, $contents);
            $this->fs->write('app/Model/' . $class . '.php', $contents);
            
            $this->writeln('[x] model : ' . $class . ' -- created', 'green');
        }
        
        # create the db file with schema
        if ($this->fs->has('storage/db/' . $file)) {
            $this->writeln('[ ] db : ' . $file . ' -- already exists', 'yellow');
        } else {
            $this->fs->write('storage/db/' . $file, '');
            $cname = 'App\\Model\\' . $class;
            $m = new $cname();
            $m->create();
            $this->writeln('[x] db : ' . $file . ' -- created', 'green');
        }        
    }
    
    function model_list($args)
    {
        $list = $this->fs->listContents('app/Model/');
    
        foreach($list as $item) {
            $this->writeln('- ' . str_replace('.php', '', $item['basename']));
        }       
    }
    
    function model_info($args)
    {
        $class = isset($args[0]) ? $args[0] : null;
        if (!$class) {
            $this->help('model:info');
            exit;
        }
        
        if (file_exists(ROOT . 'app/Model/' . ucfirst($class) . '.php')) {
            $cname = 'App\\Model\\' . ucfirst($class);
            $class = new $cname();
        
            $schema = $class->getSchema();
            $this->writeln('SCHEMA');
            $this->writeln($schema, 'green');
        
            $this->writeln('METHODS');
            $methods = get_class_methods($class);
            foreach($methods as $method) {
                if (strpos($method, '_') === 0)
                    continue;
                
                $this->writeln('- ' . $method, 'green');
            }        
        } else {
            $this->writeln('[ ] model : ' . ucfirst($class) . ', does not exist', 'red');
        }
    }    
    
    function createClassFile($class)
    {
        # create the model file
        if ($this->fs->has('app/Model/' . $class . '.php')) {
            $this->writeln('[ ] model : ' . $class . ' -- already exists', 'yellow');
        } else {
            $contents = $this->fs->read($path . 'app/Model/Model.php.src');
            $contents = str_replace('{$class}', $class, $contents);
            $contents = str_replace('{$file}', $file, $contents);
            $this->fs->write('app/Model/' . $class . '.php', $contents);
            
            $this->writeln('[x] model : ' . $class . ' -- created', 'green');
        }  
    }
    
    function createDbFile($file)
    {
        # create the db file with schema
        if ($this->fs->has('storage/db/' . $file)) {
            $this->writeln('[ ] db : ' . $file . ' -- already exists', 'yellow');
        } else {
            $this->fs->write('storage/db/' . $file, '');
            $cname = 'App\\Model\\' . $class;
            $m = new $cname();
            $m->create();
            $this->writeln('[x] db : ' . $file . ' -- created', 'green');
        }      
    }
}
