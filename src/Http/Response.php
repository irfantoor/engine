<?php

namespace IrfanTOOR\Engine\Http;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Http\Message;

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
class Response extends Message implements StatusCodeInterface, ResponseInterface
{

    protected static $phrases = [
        // Informational 1xx
        self::STATUS_CONTINUE => 'CONTINUE',
        self::STATUS_SWITCHING_PROTOCOLS => 'SWITCHING_PROTOCOLS',
        self::STATUS_PROCESSING => 'PROCESSING',

        // Successful 2xx
        self::STATUS_OK => 'OK',
        self::STATUS_CREATED => 'CREATED',
        self::STATUS_ACCEPTED => 'ACCEPTED',
        self::STATUS_NON_AUTHORITATIVE_INFORMATION => 'NON_AUTHORITATIVE_INFORMATION',
        self::STATUS_NO_CONTENT => 'NO_CONTENT',
        self::STATUS_RESET_CONTENT => 'RESET_CONTENT',
        self::STATUS_PARTIAL_CONTENT => 'PARTIAL_CONTENT',
        self::STATUS_MULTI_STATUS => 'MULTI_STATUS',
        self::STATUS_ALREADY_REPORTED => 'ALREADY_REPORTED',
        self::STATUS_IM_USED => 'IM_USED',

        // Redirection 3xx
        self::STATUS_MULTIPLE_CHOICES => 'MULTIPLE_CHOICES',
        self::STATUS_MOVED_PERMANENTLY => 'MOVED_PERMANENTLY',
        self::STATUS_FOUND => 'FOUND',
        self::STATUS_SEE_OTHER => 'SEE_OTHER',
        self::STATUS_NOT_MODIFIED => 'NOT_MODIFIED',
        self::STATUS_USE_PROXY => 'USE_PROXY',
        self::STATUS_RESERVED => 'RESERVED',
        self::STATUS_TEMPORARY_REDIRECT => 'TEMPORARY_REDIRECT',
        self::STATUS_PERMANENT_REDIRECT => 'PERMANENT_REDIRECT',

        // Client Errors 4xx
        self::STATUS_BAD_REQUEST => 'BAD_REQUEST',
        self::STATUS_UNAUTHORIZED => 'UNAUTHORIZED',
        self::STATUS_PAYMENT_REQUIRED => 'PAYMENT_REQUIRED',
        self::STATUS_FORBIDDEN => 'FORBIDDEN',
        self::STATUS_NOT_FOUND => 'NOT_FOUND',
        self::STATUS_METHOD_NOT_ALLOWED => 'METHOD_NOT_ALLOWED',
        self::STATUS_NOT_ACCEPTABLE => 'NOT_ACCEPTABLE',
        self::STATUS_PROXY_AUTHENTICATION_REQUIRED => 'PROXY_AUTHENTICATION_REQUIRED',
        self::STATUS_REQUEST_TIMEOUT => 'REQUEST_TIMEOUT',
        self::STATUS_CONFLICT => 'CONFLICT',
        self::STATUS_GONE => 'GONE',
        self::STATUS_LENGTH_REQUIRED => 'LENGTH_REQUIRED',
        self::STATUS_PRECONDITION_FAILED => 'PRECONDITION_FAILED',
        self::STATUS_PAYLOAD_TOO_LARGE => 'PAYLOAD_TOO_LARGE',
        self::STATUS_URI_TOO_LONG => 'URI_TOO_LONG',
        self::STATUS_UNSUPPORTED_MEDIA_TYPE => 'UNSUPPORTED_MEDIA_TYPE',
        self::STATUS_RANGE_NOT_SATISFIABLE => 'RANGE_NOT_SATISFIABLE',
        self::STATUS_EXPECTATION_FAILED => 'EXPECTATION_FAILED',
        self::STATUS_IM_A_TEAPOT => 'IM_A_TEAPOT',
        self::STATUS_MISDIRECTED_REQUEST => 'MISDIRECTED_REQUEST',
        self::STATUS_UNPROCESSABLE_ENTITY => 'UNPROCESSABLE_ENTITY',
        self::STATUS_LOCKED => 'LOCKED',
        self::STATUS_FAILED_DEPENDENCY => 'FAILED_DEPENDENCY',
        self::STATUS_UPGRADE_REQUIRED => 'UPGRADE_REQUIRED',
        self::STATUS_PRECONDITION_REQUIRED => 'PRECONDITION_REQUIRED',
        self::STATUS_TOO_MANY_REQUESTS => 'TOO_MANY_REQUESTS',
        self::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE => 'REQUEST_HEADER_FIELDS_TOO_LARGE',
        self::STATUS_UNAVAILABLE_FOR_LEGAL_REASONS => 'UNAVAILABLE_FOR_LEGAL_REASONS',

        // Server Errors 5xx
        self::STATUS_INTERNAL_SERVER_ERROR => 'INTERNAL_SERVER_ERROR',
        self::STATUS_NOT_IMPLEMENTED => 'NOT_IMPLEMENTED',
        self::STATUS_BAD_GATEWAY => 'BAD_GATEWAY',
        self::STATUS_SERVICE_UNAVAILABLE => 'SERVICE_UNAVAILABLE',
        self::STATUS_GATEWAY_TIMEOUT => 'GATEWAY_TIMEOUT',
        self::STATUS_VERSION_NOT_SUPPORTED => 'VERSION_NOT_SUPPORTED',
        self::STATUS_VARIANT_ALSO_NEGOTIATES => 'VARIANT_ALSO_NEGOTIATES',
        self::STATUS_INSUFFICIENT_STORAGE => 'INSUFFICIENT_STORAGE',
        self::STATUS_LOOP_DETECTED => 'LOOP_DETECTED',
        self::STATUS_NOT_EXTENDED => 'NOT_EXTENDED',
        self::STATUS_NETWORK_AUTHENTICATION_REQUIRED => 'NETWORK_AUTHENTICATION_REQUIRED',
    ];

    protected $code;
    protected $phrase;

    function __construct($code = self::STATUS_OK)
    {
        $this->code   = $this->validate('code', $code);
        $this->phrase = $this->getReasonPhrase($this->code);

        // constructs the message
        parent::__construct();
    }

    function validate($name, $value)
    {
        if (defined('HACKER_MODE') && HACKER_MODE !== false)
            return $value;

        $$name = $value;

        switch($name) {
            case 'code':
                $code = (int) $code;
                if (!array_key_exists($code, self::$phrases))
                    throw new Exception('status code: ' . $code . ', is not valid');
                return $code;

            default:
                return parent::validate($name, $value);
        }
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
        return $this->code;
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
    public function withStatus($code, $phrase = null)
    {
        $code   = $this->validate('code', $code);
        $phrase = $phrase ?: $this->phrase;

        if ($phrase === $this->phrase && $code === $this->code) {
            return $this;
        }

        $clone = clone $this;
        $clone->code = $code;
        $clone->phrase = $phrase;
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
         $code = $this->getStatusCode();
         $phrase = $this->phrase ?: (self::$phrases[$code] ?: 'NOT_DEFINED');
         return $phrase;
     }

    /**
    * Sends this response
    *
    */
    function send()
    {
        $http_line = sprintf('HTTP/%s %s %s',
            $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getReasonPhrase()
        );

        header($http_line, true, $this->getStatusCode());

        foreach ($this->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }

        $stream = $this->getBody();

        // if ($stream->isSeekable()) {
        //     $stream->rewind();
        // }
        //
        // while (!$stream->eof()) {
        //     echo $stream->read(1024 * 8);
        // }
        //

        echo (string) $stream;

        die();
    }
}
