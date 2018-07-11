<?php

namespace App\Controller;

use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Controller;
use IrfanTOOR\Engine\Session;
use IrfanTOOR\Engine\View;
use Latte\Engine as Latte;

class WelcomeController extends Controller
{
    function __construct($args)
    {
        parent::__construct($args);        
    }

    function defaultMethod($request, $response, $args)
    {
        if ($this->isLogged()) {
            # register an event
            # register an event
            $this->register('menu', function() {
                $view = new View($this);
                echo $view->process('auth/menu');
            });
        }
    
        $this->set([
            'ie_name' => "Irfan's Engine",
            'user'    => $this->loggedUser(),
        ]);
        
        $action = $args ? $args[0] : null;

        switch ($action) {
            case 'phpinfo':
                if ($this->isLogged()) {
                    ob_start();
                    phpinfo();
                    $contents = ob_get_clean();
                    preg_match('#\<body\>(.*)\<\/body\>#Us', $contents, $m);
                
                    $this->set('contents', $m[1]);
                } else {
                    $this->set('contents', 'You need to login to view this page!');
                }
                
                break;
                
            case 'env':
                ob_start();
                Debug::dump($this->engine()->environment());
                $contents = ob_get_clean();
                $this->set('contents', $contents);
                break;

            case 'irfans-engine':
                $contents = @file_get_contents(ROOT . 'vendor/irfantoor/engine/README.md');
                if (!$contents)
                    $contents = @file_get_contents(ROOT . 'README.md');
                
                $this->set('contents', '<pre>' . $contents . '</pre>');
                break;
                
            default:
            
        }
        return $this->show($response, 'welcome');
    } 
}
