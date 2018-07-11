<?php

namespace IrfanTOOR\Engine;

require 'vendor/autoload.php';


$r = new Http\Request([
    'uri' => 'https://irfantoor.com/',
]);

$r->send();

