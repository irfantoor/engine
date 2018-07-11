<?php

namespace App\Controller\Admin;

use IrfanTOOR\Engine\Controller;

class DashboardController extends Controller
{
    public function __construct($args)
    {
        parent::__construct($args);
    }

    public function process($request, $response, $args)
    {
        $this->set([
            'ie_name'  => 'Irfan\'s Engine',
            'user'     => $this->session()->get('user'),
            'contents' => '',
        ]);
        return $this->show($response, 'admin/dashboard');
    }
}
