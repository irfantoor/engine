<?php

namespace IrfanTOOR\Engine\Http;

use InvalidArgumentException;
use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Stream;
# use Psr\Http\Message\StreamInterface;

class Message extends Collection
{
    public function __construct($version = '1.1', $headers = null, $body = null)
    {
        $this->version = $version;

        if (!$headers)
            $headers = [];

        if (!($headers instanceof Headers)) {
            if (!is_array($headers))
                throw new InvalidArgumentException("Array or Header::class expeced as $headers", 1);

            $headers = new Headers($headers);
        }

        if ($body === null || is_string($body)) {
            $stream = new Stream(fopen('php://temp', 'w+'));
            $stream->write($body);
            $body = &$stream;
        } elseif (!($body instanceof Stream)) {
            throw new InvalidArgumentException("body can only be supplied as a stream or string", 1);
        }

        $this->set([
            'version' => $version,
            'headers' => $headers,
            'body'    => $body,
        ]);
    }

    public function __clone() {
        $this->set('headers', clone $this->get('headers'));
        $this->set('body', clone $this->get('body'));
    }

    public function getProtocolVersion()
    {
        return $this->get('version', '');
    }

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
        return $this->get('headers')->toArray();
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @importdoc
     */
    public function hasHeader($name)
    {
        #return isset($this->headers[strtolower($name)]);
        return $this->get('headers')->has($name);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * @importdoc
     */
    public function getHeader($name)
    {
        return $this->get('headers')->get($name, []);
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
        $clone->get('headers')->set($name, $value);
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
        $clone->get('headers')->add($name, $value);
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
        $clone->get('headers')->remove($name);
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
        return $this->get('body');
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
        $clone = clone $this;
        $clone->set('body', $body);
        return $clone;
    }
}
