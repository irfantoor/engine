<?php

namespace IrfanTOOR\Engine;

use IrfanTOOR\Engine\Logger;

class Exception extends \Exception
{
    public function __construct($message, $level = 0)
    {
        parent::__construct($message);

        # truncate the long path of file
        $file = $this->getFile();
        $x = explode('/', $file);
        $l = count($x);
        $file = ($l>1) ? $x[$l-2] . '/' . $x[$l-1] : $file;

        # line number
        $line = $this->getLine();

        # create the logger and log!
        $logger = new Logger('storage/log-' . date('Y-m-d') . '.log');
        $logger->log("$message, file: $file, line: $line", $level);
    }
}
