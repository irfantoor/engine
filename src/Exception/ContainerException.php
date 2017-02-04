<?php

namespace IrfanTOOR\Engine\Exception;

use IrfanTOOR\Engine\Exception;
use Interop\Container\Exception\ContainerException as InteropContainerException;

/**
 * Base interface representing a generic exception in a container.
 */
class ContainerException extends Exception implements InteropContainerException
{
}
