<?php

require 'autoload.php';

use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Debug;

Debug::enable(1);

$env = new Environment(['HELLO' => 'World!']);
Debug::dump($env);
