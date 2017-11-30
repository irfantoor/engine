<?php

namespace IrfanTOOR;

class Environment extends Collection
{
    protected static
        $instance = null;

    function __construct($mock=null)
    {
        if (!$mock)
            $mock = [];

        self::$instance = $this;

        # Environment
        $data = array_merge(
            $_SERVER,
            $mock
            # ['session' => $_SESSION]
        );

        parent::__construct($data);
        $this->lock();
    }

    static function getInstance()
    {
        return self::$instance ?: new Environment();
    }
}
