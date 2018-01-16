<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Http\Stream;
use IrfanTOOR\Engine\Debug;

Debug::enable(1);

$s = Stream::createFromString('');

$s->write('Hello');
$s->write(' ');
$s->write('World!');
Debug::dump((string) $s);
