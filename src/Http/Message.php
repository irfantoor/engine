<?php

namespace IrfanTOOR\Engine\Http;

use InvalidArgumentException;
use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Stream;


class Message extends Collection
{
    /**
     * A map of valid protocol versions
     *
     * @var array
     */
    protected static $valid_protocol_versions = [
        '1.0' => true,
        '1.1' => true,
        '2.0' => true,
        '2' => true,
    ];

    protected static $defaults = [
        'version' => '1.1',
        'headers' => null,
        'body'    => null,
    ];

    public function __construct()
    {
        parent::__construct(self::$defaults);
        $this->_process();
    }

    /**
     * process calls like withScheme, withHost ...
     */
    private function _with($what, $args=[])
    {
        if (($c = count($args)) == 1) {
            if ($args[0] === $this->get($what)) {
                return $this;
            }
        } else {
            if ($args[1] === $this->get($what)->get($args[0])) {
                return $this;
            }
        }

        $clone = clone $this;

        if ($c == 1) {
            $clone->set($what, $args[0]);
        } else {
            $clone->get($what)->set($args[0], $args[1]);
        }
        print_r([$clone]);

        return $clone;
    }

    /*
     * Process the contents after a call of type withXxxx e.g. withPort(8080)
     */
    private function _process() {
        # Extract
        extract ($uri = $this->toArray());

        # Validate/normalize the data
        foreach($uri as $k => $v) {
            switch($k) {
                case 'version':
                    if (!isset(self::$valid_protocol_versions[$version])) {
                        throw new InvalidArgumentException(
                            'Invalid HTTP version. Must be one of: '
                            . implode(', ', array_keys(self::$valid_protocol_versions))
                        );
                    }
                    break;

                case 'headers':
                    if (!$headers)
                        $headers = new Headers();
                    break;

                case 'body':
                    if (!$body)
                        $body = new Stream();
                    break;

                default:
                    # nothing to filter :)
            }
        }

        # Process

        # Update
        foreach($uri as $k=>$v)
            $this->set($k, $$k);
    }

    /**
     * @importdoc
     */
    public function getProtocolVersion()
    {
        return $this->get('version');
    }

    /**
     * @importdoc
     */
    public function withProtocolVersion($version)
    {
        return $this->_with('version', [$version]);
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
        return $this->get('headers');
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @importdoc
     */
    public function hasHeader($name)
    {
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
        return implode(',', $this->get('headers')->get($name, []));
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * @importdoc
     */
    public function withHeader($name, $value)
    {
        return $this->_with('header', [$name, $value]);
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * @importdoc
     */
    public function withAddedHeader($name, $value)
    {
        $v = $this->get('headers')->get($name, []);
        $v[] = $value;

        return $this->withHeader($name, $value);
    }

    /**
     * Return an instance without the specified header.
     *
     * @importdoc
     */
    public function withoutHeader($name)
    {
        $headers = $this->get('headers');
        if (!$headers->has($name))
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
}
