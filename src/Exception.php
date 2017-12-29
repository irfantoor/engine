<?php

namespace IrfanTOOR\Engine;

use Exception as PhpException;
/**
 * Exception
 */
class Exception extends PhpException
{
    function __construct($args)
    {
        parent::__construct($args);
    }
}
