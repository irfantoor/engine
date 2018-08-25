<?php

namespace IrfanTOOR\Engine;

use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\Adapter\Local;

class Filesystem
{
    protected $fs;

    public function __construct($path)
    {
        $adapter  = new Local($path, LOCK_EX, Local::SKIP_LINKS);
        $this->fs = new LeagueFilesystem($adapter);
    }

    public function __call($method, $args)
    {
        if (method_exists($this->fs, $method)) {
            return call_user_func_array([$this->fs, $method], $args);
        }

        throw new Exception("Method $method is not a valid method");
    }
}
