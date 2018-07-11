<?php

namespace App\Middleware;

use App\Model\Users;
use IrfanTOOR\Engine\Middleware;
use IrfanTOOR\Engine\Response;
use IrfanTOOR\Engine\Session;
use IrfanTOOR\Engine\View;

class AuthMiddleware extends Middleware
{
    protected $session;
    
    function __construct($controller)
    {
        parent::__construct($controller);
    }

    function process($request, $response, $args)
    {
        $action = isset($args[1]) ? $args[1] : null;
        $auth_actions = '|login|logout|'; #forgot|register|';

        if ($this->controller->isLogged()) {
            if ($action === 'logout') {
                return $this->$action($request, $response, $args);
            }

            # register an event
            $c = $this->controller;
            $this->controller->register('menu', function() use($c) {
                $view = new View($c);
                $output = $view->process('auth/menu');
                echo $output;
            });

            return $response;
        } else {
            if ($action && strpos($auth_actions, '|' . $action . '|') !== false) {
                return $this->$action($request, $response, $args);
            }
            $this->redirectTo('/admin/login');
        }
    }

    public function register($request, $response, $args)
    {
        $name = $request->get('post.name', null);
        $email = $request->get('post.email', null);
        $password = $request->get('post.password', null);

        if ($name) {
            $users = new Users;
            $user = [
                'name'     => $name,
                'email'    => $email,
                'password' => md5($password),
                'token'    => md5($email . $password . mktime()),
            ];

            if ( $users->register($user)) {
                $this->set(
                    [
                        'status' => 'success',
                        'message' => 'Your account has been created! an email
                has been sent to your email, kindly activate your account by
                clcking on the link sent to you',
                    ]
                );
            } else {
                $this->set(
                    [
                        'status' => 'danger',
                        'message' => 'Error processing the request, try later',
                    ]
                );
            }
        }

        return $this->show($response, 'auth/register');
    }

    public function login($request, $response, $args)
    {
        if ($this->controller->isLogged())
            $this->redirectTo('/admin');

        $email    = $request->get('post.email', null);
        $password = $request->get('post.password', null);
        $this->set('message', null);
        if ($email) {
            $users = new Users;
            if ($users->authenticate($email, $password)) {
                $user = $users->getFirst(
                    ['where' => 'email=:email'], 
                    ['email' => $email]
                );
                
                $this->controller->session()->set([
                    'logged' => 1,
                    'user'   => $user['name'],
                ]);
                
                $this->redirectTo('/admin', 200);
            } else {
                $this->set([
                    'status' => 'danger',
                    'message' => 'Wrong Credentials',
                ]);
            }
        }
        
        $this->show($response, 'auth/login');
    }

    public function logout($request, $response, $args)
    {
        $session = $this->controller->session();
        $session->destroy();

        $this->redirectTo('/');
    }

    public function forgot($request, $response, $args)
    {
        $data = $this->config('data');
        $data['engine'] = $this->controller->engine();

        $this->show($response, 'auth/forgot');
    }

    private function show($response, $view)
    {
        $response = $this->controller->show($response, $view);
        $response->send();
        exit;
    }
}
