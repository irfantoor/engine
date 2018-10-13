<?php

namespace IrfanTOOR\Engine;

use IrfanTOOR\Engine\Logger;

class Exception extends \Exception
{
    protected static $log_enabled = false;
    protected static $log_file    = '/dev/stderr';
    
    public static function log($file = null)
    {
        self::$log_enabled = true;
        if ($file && is_string($file)) {
            self::$log_file = $file;
        }
    }

    /**
     * Exception constructor
     */
    public function __construct($message, $level = 0)
    {
        parent::__construct($message);

        if (self::$log_enabled) {
            # truncate the long path of file
            $file = $this->getFile();
            $x = explode('/', $file);
            $l = count($x);
            $file = ($l>1) ? $x[$l-2] . '/' . $x[$l-1] : $file;

            # line number
            $line = $this->getLine();

            # create the logger and log!
            $logger = new Logger(self::$log_file);
            $logger->log("$message, file: $file, line: $line", $level);
        }
    }
}
