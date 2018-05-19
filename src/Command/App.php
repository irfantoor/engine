<?php

namespace IrfanTOOR\Engine\Command;

use IrfanTOOR\Console\Command;
use IrfanTOOR\Engine\Filesystem;

class App extends Command
{
    protected $fs;
    
    function __construct()
    {
        $this->fs = new Filesystem(ROOT);
        
        $this->help = [
            'Application console',
            'app:init' => [
                'Inititialize the application structure',
                '
Application Init

FORMAT
------
app:init [auth|cache|log]

OPTIONS
-------
auth     Initialize the authentication
cache    Initilizes the caching
log      Intializes the logging

Note: if no option is provided the basic app framework is initialized
'
            ],
            'app:serve' => 'Serves the application',
            'app:up'    => 'Put the app in service mode',
            'app:down'  => 'Put the app in maintenance mode',
            'app:test'  => 'Test Application',
        ];
        
        parent::__construct();
    }
    
    function _create_folders($folders)
    {
        $this->writeln("Creating folders");
        
        foreach($folders as $folder) {
            if ($this->fs->has($folder)) {
                $this->writeln('[ ] folder : ' . $folder . ' -- already exists', 'yellow');
            } else {
                if ($this->fs->createDir($folder)) {
                    $this->writeln('[x] folder : ' . $folder . ' -- created', 'green');
                } else {
                    $this->writeln('[ ] folder : ' . $folder . ' -- not created', 'red');
                }
            }
        }    
    }
    
    function _create_files($files)
    {
        $this->writeln("Creating files");
        
        $path = dirname(__FILE__) . '/';
        if (strpos($path, ROOT) === 0) {
            $path = substr($path, strlen(ROOT));
        }
        
        foreach($files as $file) {
            if ($this->fs->has($file)) {
                $this->writeln('[ ] file : ' . $file . ' -- already exists', 'yellow');
            } else {
                if ($this->fs->copy($path . $file . '.src', $file)) {
                    $this->writeln('[x] file : ' . $file . ' -- created', 'green');
                } else {
                    $this->writeln('[ ] file : ' . $file . ' -- not created', 'red');
                }
            }
        }    
    }     
    
    function app_down()
    {
        if ($this->fs->has('app/.maintenance')) {
            $this->writeln('[ ] app already in maintenance mode', 'yellow');
        } else {
            $this->fs->write('app/.maintenance', '');
            $contents = $this->fs->read('public/index.php');
            $contents = str_replace(
                            'bootstrap.php', 
                            'views/maintenance.php', 
                            $contents
                        );
            $this->fs->put('public/index.php', $contents);
            $this->writeln('[x] app set in maintenance mode', 'green');
        }
    }

    function app_up()
    {
        if ($this->fs->has('app/.maintenance')) {
            $this->fs->delete('app/.maintenance');
            $contents = $this->fs->read('public/index.php');
            $contents = str_replace(
                            'views/maintenance.php', 
                            'bootstrap.php',
                            $contents
                        );
            $this->fs->put('public/index.php', $contents);            
            $this->writeln('[x] app set in service mode', 'green');
        } else {
            $this->writeln('[ ] app already in service mode', 'yellow');
        }
    }
    
    function app_serve()
    {
        system('php -S localhost:8000 -t ' . ROOT . 'public/');
    }    
    
    function app_test($args) {
        system(ROOT . 'vendor/bin/phpunit --testdox --bootstrap ' . ROOT . 'app/autoload.php');
    }
    
    function app_init($args)
    {
        $action = isset($args[0]) ? $args[0] : '';
        
        switch ($action) {
            case '':
                $this->_create_folders(
                    [
                        'app',
                        'app/command',
                        'app/controller',
                        'app/model',
                        'app/views',
                        'public',
                        'storage',
                        'storage/cache',
                        'storage/db',
                        'storage/ds',
                        'storage/tmp',
                    ]
                );
                
                $this->_create_files(
                    [
                        'app/autoload.php',
                        'app/bootstrap.php',
                        'app/config.php',
                        'app/controller/WelcomeController.php',
                        'app/views/footer.php',
                        'app/views/header.php',
                        'app/views/maintenance.php',
                        'app/views/welcome.php',
                        'public/favicon.ico',
                        'public/index.php',
                        'public/robots.txt',
                    ]
                );
                break;
                
            case 'auth':
                $this->_create_files(
                    [
                        'app/controller/AdminController.php',
                        'app/controller/admin/DashboardController.php',
                        'app/middleware/AuthMiddleware.php',
                        'app/model/Sessions.php',
                        'app/model/Users.php',
                        'app/views/admin/dashboard.php',
                        'app/views/auth/forgot.php',
                        'app/views/auth/login.php',
                        'app/views/auth/menu.php',
                        'app/views/auth/register.php',
                        
                        'storage/db/sessions.sqlite',
                        'storage/db/users.sqlite',
                    ]
                );
                
                $m = new \App\Model\Sessions();
                try {
                    $m->create();
                    $this->writeln('[x] db : sessions.sqlite -- created', 'green');
                } catch(\Exception $e) {
                    $this->writeln('[ ] db : sessions.sqlite -- already exists', 'yellow');
                }
                
                $m = new \App\Model\Users();
                try {
                    $m->create();
                    $this->writeln('[x] db : users.sqlite -- created', 'green');
                } catch(\Exception $e) {
                    $this->writeln('[ ] db : users.sqlite -- already exists', 'yellow');
                }

                break;
            
            case 'cache':
                $this->_create_files(
                    [
                        'app/middleware/CacheMiddleware.php',
                        'app/model/Cache.php',
                    ]
                );
                $m = new Model;
                $m->model_create(['cache','cache']);
                
                break;
                
            case 'log':
                $this->_create_files(
                    [
                        'app/middleware/LogMiddleware.php',
                    ]
                );
                break;
                
            default:
                $this->help(['app:init']);
        }
    }
    
    static function copyIe()
    {        
        $src = dirname(dirname(__DIR__));
        $vendor = $src . '/vendor';
        $src .= '/ie';
        
        # in the ROOT folder
        $dst = dirname($vendor) . '/ie';
        if ($src !== $dst && !file_exists($dst))
            symlink($src, $dst);
        
        # in the bin folder
        $dst = $vendor . '/bin';
        if (!is_dir($dst)) {
            mkdir($dst);
        }
        if (!file_exists($dst))
            symlink('../irfantoor/engine/ie', $dst);
    }   
}
