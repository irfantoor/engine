<?php

namespace IrfanTOOR\Engine\Http;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use IrfanTOOR\Engine;
use IrfanTOOR\Engine\Http\Message;
use IrfanTOOR\Engine\Http\Stream;
// use Psr\Http\Message\ResponseInterface;

/**
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
class Response extends Message Implements StatusCodeInterface
{
    protected $status;
    protected $phrase;

    protected static $phrases = [
        // Informational 1xx
        100 => 'CONTINUE',
        101 => 'SWITCHING_PROTOCOLS',
        102 => 'PROCESSING',

        // Successful 2xx
        200 => 'OK',
        201 => 'CREATED',
        202 => 'ACCEPTED',
        203 => 'NON_AUTHORITATIVE_INFORMATION',
        204 => 'NO_CONTENT',
        205 => 'RESET_CONTENT',
        206 => 'PARTIAL_CONTENT',
        207 => 'MULTI_STATUS',
        208 => 'ALREADY_REPORTED',
        226 => 'IM_USED',

        // Redirection 3xx
        300 => 'MULTIPLE_CHOICES',
        301 => 'MOVED_PERMANENTLY',
        302 => 'FOUND',
        303 => 'SEE_OTHER',
        304 => 'NOT_MODIFIED',
        305 => 'USE_PROXY',
        306 => 'RESERVED',
        307 => 'TEMPORARY_REDIRECT',
        308 => 'PERMANENT_REDIRECT',

        // Client Errors 4xx
        400 => 'BAD_REQUEST',
        401 => 'UNAUTHORIZED',
        402 => 'PAYMENT_REQUIRED',
        403 => 'FORBIDDEN',
        404 => 'NOT_FOUND',
        405 => 'METHOD_NOT_ALLOWED',
        406 => 'NOT_ACCEPTABLE',
        407 => 'PROXY_AUTHENTICATION_REQUIRED',
        408 => 'REQUEST_TIMEOUT',
        409 => 'CONFLICT',
        410 => 'GONE',
        411 => 'LENGTH_REQUIRED',
        412 => 'PRECONDITION_FAILED',
        413 => 'PAYLOAD_TOO_LARGE',
        414 => 'URI_TOO_LONG',
        415 => 'UNSUPPORTED_MEDIA_TYPE',
        416 => 'RANGE_NOT_SATISFIABLE',
        417 => 'EXPECTATION_FAILED',
        418 => 'IM_A_TEAPOT',
        421 => 'MISDIRECTED_REQUEST',
        422 => 'UNPROCESSABLE_ENTITY',
        423 => 'LOCKED',
        424 => 'FAILED_DEPENDENCY',
        426 => 'UPGRADE_REQUIRED',
        428 => 'PRECONDITION_REQUIRED',
        429 => 'TOO_MANY_REQUESTS',
        431 => 'REQUEST_HEADER_FIELDS_TOO_LARGE',
        451 => 'UNAVAILABLE_FOR_LEGAL_REASONS',

        // Server Errors 5xx
        500 => 'INTERNAL_SERVER_ERROR',
        501 => 'NOT_IMPLEMENTED',
        502 => 'BAD_GATEWAY',
        503 => 'SERVICE_UNAVAILABLE',
        504 => 'GATEWAY_TIMEOUT',
        505 => 'VERSION_NOT_SUPPORTED',
        506 => 'VARIANT_ALSO_NEGOTIATES',
        507 => 'INSUFFICIENT_STORAGE',
        508 => 'LOOP_DETECTED',
        510 => 'NOT_EXTENDED',
        511 => 'NETWORK_AUTHENTICATION_REQUIRED',
    ];

    function __construct($init = [])
    {
        $env = new Environment();

        # defaults
        $defaults = [
            'version' => str_replace('HTTP/', '', $env['SERVER_PROTOCOL']),
            'headers' => [],
            'body'    => '',
            'status'  => self::STATUS_OK,
            'phrase'  => '',
        ];

        foreach ($defaults as $k=>$v) {
            if (isset($init[$k])) {
                $defaults[$k] = $init[$k];
            } else {
                $init[$k] = $v;
            }
        }

        $init['headers']['Engine'] = [Engine::NAME . ' ' . Engine::VERSION];

        parent::__construct($init);
        $this->status = $defaults['status'];
        $this->phrase = $defaults['phrase'];
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $clone = clone $this;
        $clone->status = $code;
        $clone->phrase = $reasonPhrase;

        return $clone;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        if ($this->phrase)
            return $this->phrase;

        if (isset(self::$phrases[$this->status]))
            return self::$phrases[$this->status];

        return 'NOT_DEFINED';
    }

    function send()
    {
        $status = $this->getStatusCode();
        $http_line = sprintf('HTTP/%s %s %s',
            $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getReasonPhrase()
        );

        header($http_line, true, $status);
        $this->headers->send();

        $stream = $this->getBody();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        while (!$stream->eof()) {
            echo $stream->read(1024 * 8);
        }

        $stream->close();

        exit;
    }
}
