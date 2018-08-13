<?php

namespace IrfanTOOR\Engine;

require 'vendor/autoload.php';

use Tracy\Debugger;
Debugger::enable();

$r = new Http\Request([
    'uri' => 'https://irfantoor.com/',
]);

$r->send();

