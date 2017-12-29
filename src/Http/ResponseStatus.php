<?php
/**
 * IrfanTOOR\Smart
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/collection/blob/master/LICENSE (MIT License)
 */

namespace IrfanTOOR\Engine\Http;

use Fig\Http\Message\StatusCodeInterface;

/**
 * Status
 */
class ResponseStatus implements StatusCodeInterface
{
    protected $code;

    protected static $phrases = [
        // Informational 1xx
        self::STATUS_CONTINUE => 'STATUS_CONTINUE',
        self::STATUS_SWITCHING_PROTOCOLS => 'STATUS_SWITCHING_PROTOCOLS',
        self::STATUS_PROCESSING => 'STATUS_PROCESSING',

        // Successful 2xx
        self::STATUS_OK => 'STATUS_OK',
        self::STATUS_CREATED => 'STATUS_CREATED',
        self::STATUS_ACCEPTED => 'STATUS_ACCEPTED',
        self::STATUS_NON_AUTHORITATIVE_INFORMATION => 'STATUS_NON_AUTHORITATIVE_INFORMATION',
        self::STATUS_NO_CONTENT => 'STATUS_NO_CONTENT',
        self::STATUS_RESET_CONTENT => 'STATUS_RESET_CONTENT',
        self::STATUS_PARTIAL_CONTENT => 'STATUS_PARTIAL_CONTENT',
        self::STATUS_MULTI_STATUS => 'STATUS_MULTI_STATUS',
        self::STATUS_ALREADY_REPORTED => 'STATUS_ALREADY_REPORTED',
        self::STATUS_IM_USED => 'STATUS_IM_USED',

        // Redirection 3xx
        self::STATUS_MULTIPLE_CHOICES => 'STATUS_MULTIPLE_CHOICES',
        self::STATUS_MOVED_PERMANENTLY => 'STATUS_MOVED_PERMANENTLY',
        self::STATUS_FOUND => 'STATUS_FOUND',
        self::STATUS_SEE_OTHER => 'STATUS_SEE_OTHER',
        self::STATUS_NOT_MODIFIED => 'STATUS_NOT_MODIFIED',
        self::STATUS_USE_PROXY => 'STATUS_USE_PROXY',
        self::STATUS_RESERVED => 'STATUS_RESERVED',
        self::STATUS_TEMPORARY_REDIRECT => 'STATUS_TEMPORARY_REDIRECT',
        self::STATUS_PERMANENT_REDIRECT => 'STATUS_PERMANENT_REDIRECT',

        // Client Errors 4xx
        self::STATUS_BAD_REQUEST => 'STATUS_BAD_REQUEST',
        self::STATUS_UNAUTHORIZED => 'STATUS_UNAUTHORIZED',
        self::STATUS_PAYMENT_REQUIRED => 'STATUS_PAYMENT_REQUIRED',
        self::STATUS_FORBIDDEN => 'STATUS_FORBIDDEN',
        self::STATUS_NOT_FOUND => 'STATUS_NOT_FOUND',
        self::STATUS_METHOD_NOT_ALLOWED => 'STATUS_METHOD_NOT_ALLOWED',
        self::STATUS_NOT_ACCEPTABLE => 'STATUS_NOT_ACCEPTABLE',
        self::STATUS_PROXY_AUTHENTICATION_REQUIRED => 'STATUS_PROXY_AUTHENTICATION_REQUIRED',
        self::STATUS_REQUEST_TIMEOUT => 'STATUS_REQUEST_TIMEOUT',
        self::STATUS_CONFLICT => 'STATUS_CONFLICT',
        self::STATUS_GONE => 'STATUS_GONE',
        self::STATUS_LENGTH_REQUIRED => 'STATUS_LENGTH_REQUIRED',
        self::STATUS_PRECONDITION_FAILED => 'STATUS_PRECONDITION_FAILED',
        self::STATUS_PAYLOAD_TOO_LARGE => 'STATUS_PAYLOAD_TOO_LARGE',
        self::STATUS_URI_TOO_LONG => 'STATUS_URI_TOO_LONG',
        self::STATUS_UNSUPPORTED_MEDIA_TYPE => 'STATUS_UNSUPPORTED_MEDIA_TYPE',
        self::STATUS_RANGE_NOT_SATISFIABLE => 'STATUS_RANGE_NOT_SATISFIABLE',
        self::STATUS_EXPECTATION_FAILED => 'STATUS_EXPECTATION_FAILED',
        self::STATUS_IM_A_TEAPOT => 'STATUS_IM_A_TEAPOT',
        self::STATUS_MISDIRECTED_REQUEST => 'STATUS_MISDIRECTED_REQUEST',
        self::STATUS_UNPROCESSABLE_ENTITY => 'STATUS_UNPROCESSABLE_ENTITY',
        self::STATUS_LOCKED => 'STATUS_LOCKED',
        self::STATUS_FAILED_DEPENDENCY => 'STATUS_FAILED_DEPENDENCY',
        self::STATUS_UPGRADE_REQUIRED => 'STATUS_UPGRADE_REQUIRED',
        self::STATUS_PRECONDITION_REQUIRED => 'STATUS_PRECONDITION_REQUIRED',
        self::STATUS_TOO_MANY_REQUESTS => 'STATUS_TOO_MANY_REQUESTS',
        self::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE => 'STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE',
        self::STATUS_UNAVAILABLE_FOR_LEGAL_REASONS => 'STATUS_UNAVAILABLE_FOR_LEGAL_REASONS',

        // Server Errors 5xx
        self::STATUS_INTERNAL_SERVER_ERROR => 'STATUS_INTERNAL_SERVER_ERROR',
        self::STATUS_NOT_IMPLEMENTED => 'STATUS_NOT_IMPLEMENTED',
        self::STATUS_BAD_GATEWAY => 'STATUS_BAD_GATEWAY',
        self::STATUS_SERVICE_UNAVAILABLE => 'STATUS_SERVICE_UNAVAILABLE',
        self::STATUS_GATEWAY_TIMEOUT => 'STATUS_GATEWAY_TIMEOUT',
        self::STATUS_VERSION_NOT_SUPPORTED => 'STATUS_VERSION_NOT_SUPPORTED',
        self::STATUS_VARIANT_ALSO_NEGOTIATES => 'STATUS_VARIANT_ALSO_NEGOTIATES',
        self::STATUS_INSUFFICIENT_STORAGE => 'STATUS_INSUFFICIENT_STORAGE',
        self::STATUS_LOOP_DETECTED => 'STATUS_LOOP_DETECTED',
        self::STATUS_NOT_EXTENDED => 'STATUS_NOT_EXTENDED',
        self::STATUS_NETWORK_AUTHENTICATION_REQUIRED => 'STATUS_NETWORK_AUTHENTICATION_REQUIRED',
    ];

    public function __construct($code = self::STATUS_OK)
    {
        $this->setStatusCode($code);
    }

    public static function validate($code)
    {
        if (array_key_exists($code, static::$phrases))
            return $code;

        return static::STATUS_BAD_REQUEST;
    }

    public static function phrase($code = self::STATUS_OK) {
        return static::$phrases[static::validate($code)];
    }

    public function setStatusCode($code)
    {
        $this->code = static::validate($code);
    }

    public function getStatusCode() {
        return $this->code;
    }

    public function __toString()
    {
        return $this->code;
    }

    public function getStatusPhrase() {
        return static::phrase($this->code);
    }
}
