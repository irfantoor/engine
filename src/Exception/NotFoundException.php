<?php

namespace IrfanTOOR\Engine\Exception;

use IrfanTOOR\Engine\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException as InteropNotFoundException;

/**
 * No entry was found in the container.
 */
class NotFoundException extends ContainerException implements InteropNotFoundException
{
}
