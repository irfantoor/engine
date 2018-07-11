<?php

namespace App\Controller;

use IrfanTOOR\Engine\Controller;

class AdminController extends Controller
{
    public function __construct($engine)
    {
        parent::__construct($engine);
        define('ADMIN_MENU', true);
        $this->addMiddleware('App\Middleware\AuthMiddleware');
    }

    /**
     * Do the routing for the existing classes or pass control to Dashboard
     */
    public function defaultMethod($request, $response, $args)
    {
        $action = isset($args[1]) ? $args[1] : '';
        switch($action) {
            case '':
            case 'dashboard':
                $class = '\\App\\Controller\\Admin\\DashboardController';
                break;

            default:
                
                $class = '\\App\\Controller\\Admin\\' . ucFirst($action) . 'Controller';
                break;
        }

        try {
            $c = new $class($this->get('engine'));
            return $c->process($request, $response, $args);
        } catch(\Exception $e) {            
            $this->redirectTo('/admin');
            exit;
        }
    }
}
