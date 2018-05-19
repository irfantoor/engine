<?php

namespace IrfanTOOR\Engine;

class FileLogger
{
    protected $file;

    function __construct($file)
    {
        $this->file = $file;
    }

    function log($message, $level = 0)
    {
        file_put_contents(
            date('Y-m-d H:i:s') . " LEVEL-{$level} $message" . PHP_EOL,
            FILE_APPEND
        );
    }
}
