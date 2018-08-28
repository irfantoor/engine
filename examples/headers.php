<?php

require 'autoload.php';

use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Debug;

Debug::enable(1);
$headers = Headers::createFromEnvironment();
Debug::dump($headers->toArray());

$headers->set('Content-Type', 'text/plain');

Debug::dump($headers->toArray());
