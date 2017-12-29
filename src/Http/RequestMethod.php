<?php
/**
 * IrfanTOOR\Smart
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/collection/blob/master/LICENSE (MIT License)
 */

namespace IrfanTOOR\Engine\Http;

use Fig\Http\Message\RequestMethodInterface;
use InvalidArgumentException;

/**
 * Method
 */
class RequestMethod implements RequestMethodInterface
{
    protected $method;

    public function __construct($method = self::METHOD_GET)
    {
        $this->setMethod($method);
    }

    /**
     * Gets a string value indicating the RequestMethod
     *
     * @return string value of the RequestMethod
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Sets a method as a RequestMethod
     *
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = self::validate($method);
    }

    /**
     * Validate that the string "GET" or  "POST" etc. is a valid method,
     * by returningn the same string if valid or 'GET' otherwise
     *
     * @param string $method  method to be validated
     * @param string $default default method to return if the $method is invalid
     *
     * @return string representing validated $method or $default otherwise
     */
    public static function validate($method)
    {
        $method = strtoupper($method);

        if (defined('self::METHOD_' . $method))
            return $method;

        throw new InvalidArgumentException('Unknown method: ' . $method);
    }
}
