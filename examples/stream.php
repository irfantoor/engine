<?php

require 'autoload.php';

use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Http\Stream;
use IrfanTOOR\Debug;

Debug::enable(1);

$s = new Stream();

$s->write('Hello');
$s->write(' ');
$s->write('World!');

Debug::dump((string) $s);

$s->seek(6);
Debug::dump($s->getContents());
