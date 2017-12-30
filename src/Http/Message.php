<?php

namespace IrfanTOOR\Engine\Http;

use InvalidArgumentException;
use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Http\Headers;
use Psr\Http\Message\StreamInterface;

class Message
{
    protected $version;
    protected $headers;
    protected $body;

    public function __construct($version = '1.1', $headers = [], $body = null)
    {
        $this->version = $version;
        $this->headers = new Headers($headers);
        $this->body = $body ? $body : new Stream(fopen('php://temp', 'rw+'));
    }

    /**
     * Disable magic setter to ensure immutability
     */
    public function __set($name, $value)
    {
        // Do nothing
    }

    /**
     * @importdoc
     */
    public function getProtocolVersion()
    {
        return $this->version;
    }

    /**
     * @importdoc
     */
    public function withProtocolVersion($version)
    {
        if ($version === $this->version)
            return $this;

        $clone = clone $this;
        $clone->version = $version;
        return $clone;
    }

    /*******************************************************************************
     * Headers
     ******************************************************************************/

    /**
     * Retrieves all message header values.
     *
     * @importdoc
     */
    public function getHeaders()
    {
        // $headers = [];
        // foreach($this->headers as $k=>$v) {
        //     $headers[$v['id']] = $v['value'];
        // }
        // return $headers;
        return $this->headers->toArray();
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @importdoc
     */
    public function hasHeader($name)
    {
        #return isset($this->headers[strtolower($name)]);
        return $this->headers->has($name);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * @importdoc
     */
    public function getHeader($name)
    {
        // $sname = strtolower($name);
        //
        // return isset($this->headers[$sname])
        //     ? $this->headers[$sname]['value']
        //     : [];
        return $this->headers->get($name, []);
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * @importdoc
     */
    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * @importdoc
     */
    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->set($name, $value);
        return $clone;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * @importdoc
     */
    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->add($name, $value);
        return $clone;
    }

    /**
     * Return an instance without the specified header.
     *
     * @importdoc
     */
    public function withoutHeader($name)
    {
        if (!$this->hasHeader($name))
            return $this;

        $clone = clone $this;
        $clone->headers->remove($name);
        return $clone;
    }

    /*******************************************************************************
     * Body
     ******************************************************************************/

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        // TODO: Test for invalid body?
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    function __clone()
    {
        # Clones Headers after PHP clone call
        $this->headers = new Headers($this->headers->toArray());
    }
}
