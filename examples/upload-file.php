<?php

require 'autoload.php';

use IrfanTOOR\Engine\Http\UploadedFile;
use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Debug;

Debug::enable(1);

$source = 'hello.txt';
$target = 'world.txt';

if (!file_exists(dirname(__FILE__) . '/' . $source)) {
    $tmp    = $source;
    $source = $target;
    $target = $tmp;
}

$file = new UploadedFile(dirname(__FILE__) . '/' . $source, $target, 'text/plain');

Debug::dump($file);
$file->moveTo(dirname(__FILE__) . '/' . $file->getClientFilename());
Debug::dump($file);
