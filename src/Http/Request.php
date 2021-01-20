<?php

namespace IrfanTOOR\Engine\Http;

use Exception;
use Fig\Http\Message\RequestMethodInterface;
use IrfanTOOR\Engine\Http\{
    Message,
    Uri
};
use Psr\Http\Message\UriInterface;

/**
 * Representation of an outgoing, client-side request.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - HTTP method
 * - URI
 * - Headers
 * - Message body
 *
 * During construction, implementations MUST attempt to set the Host header from
 * a provided URI if no Host header is provided.
 *
 * Requests are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
class Request extends Message implements RequestMethodInterface
{
    protected $method;
    protected $uri;

    function __construct($init = [])
    {
        parent::__construct($init);

        $defaults = [
            'method' => self::METHOD_GET,
            'url'    => 'http://localhost',
            'uri'    => null,
        ];

        foreach ($defaults as $k=>$v) {
            if (isset($init[$k])) {
                $defaults[$k] = $init[$k];
            }
        }

        $this->method = $defaults['method'];
        $this->uri    = $defaults['uri'] ?? new Uri($defaults['url']);
    }

    function __clone()
    {
        parent::__clone();
        $this->uri = clone $this->uri;
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    function getRequestTarget()
    {
        return (string) $this->uri;
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return static
     */
    function withRequestTarget($requestTarget)
    {
        $clone = clone $this;
        $clone->uri = new Uri($requestTarget);

        return $clone;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    function getMethod()
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    function withMethod($method)
    {
        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    function getUri()
    {
        return clone $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    function withUri(UriInterface $uri, $preserveHost = false)
    {
        # todo -- preserve host logic
        $clone = clone $this;
        $clone->uri = $uri;

        return $clone;
    }

    # todo -- send request
    # todo -- send - must return a response
    # todo -- sendAsync - must return a promise
    /*
     * Sends the request
     * 
     * returns IrfanTOOR\Engine\Http\Response
     */
    function send()
    {
        $uri = (string) $this->get('uri');
        if (!$uri)
            throw new Exception('Request: URI not defined');

        print_r("todo -- to be coded");
    }
}
