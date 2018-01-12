<?php
declare(strict_types = 1);

namespace IrfanTOOR\Engine\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterefcae;
use Psr\Http\Message\UriInterface;

/**
 * Simple class to create instances of PSR-7 classes.
 */
class Factory
{
    protected static $request_factory        = null;
    protected static $response_factory       = null;
    protected static $server_request_factory = null;
    protected static $stream_factory         = null;
    protected static $uri_factory            = null;
    protected static $uploaded_file_factory  = null;


    /**
     * Set a custom RequestFactory.
     */
    public static function setRequestFactory(
        RequestFactoryInterface $request_factory
    ) {
        self::$request_factory = $request_factory;
    }

    /**
     * Set a custom ResponseFactory.
     */
    public static function setResponseFactory(
        ResponseFactoryInterface $response_factory
    ) {
        self::$response_factory = $response_factory;
    }

    /**
     * Set a custom ServerRequestFactory.
     */
    public static function setServerRequestFactory(
        ServerRequestFactoryInterface $server_request_factory
    ) {
        self::$server_request_factory = $server_request_factory;
    }

    /**
     * Set a custom StreamFactory.
     */
    public static function setStreamFactory(
        StreamFactoryInterface $stream_factory
    ) {
        self::$stream_factory = $stream_factory;
    }

    /**
     * Set a custom UriFactory.
     */
    public static function setUriFactory(UriFactoryInterface $uriFactory)
    {
        self::$uri_factory = $uri_factory;
    }

    /**
     * Create a new request.
     *
     * @param string $method
     * @param UriInterface|string $uri
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri)
    {
        if (self::$request_factory === null) {
            self::$request_factory = new Factory\RequestFactory();
        }

        return self::$request_factory->createRequest(
            $method,
            $uri
        );
    }

    /**
     * Creates a Response instance.
     *
     * @param int $code The status code
     */
    public static function createResponse(
        int $code = 200
    ): ResponseInterface
    {
        if (self::$response_factory === null) {
            self::$response_factory = new Factory\ResponseFactory();
        }

        return self::$response_factory->createResponse($code);
    }

    /**
     * Create a new server request.
     *
     * @param string $method
     * @param UriInterface|string $uri
     *
     * @return ServerRequestInterface
     */
    public function createServerRequest(
        $method = null,
        $uri    = null
    ) : ServerRequestInterface
    {
        if (self::$server_request_factory === null) {
            self::$server_request_factory = new Factory\ServerRequestFactory();
        }

        return self::$server_request_factory->createServerRequest(
            $method,
            $uri
        );
    }

    /**
     * Create a new server request from server variables.
     *
     * @param array $server Typically $_SERVER or similar structure.
     *
     * @return ServerRequestInterface
     *
     * @throws \InvalidArgumentException
     *  If no valid method or URI can be determined.
     */
    public function createServerRequestFromArray(array $server)
    {
        if (self::$server_request_factory === null) {
            self::$server_request_factory = new Factory\ServerRequestFactory();
        }

        return self::$server_request_factory->createServerRequestFromArray(
            $server
        );
    }

    /**
     * Creates a Stream instance.
     *
     */
    public static function createStream($resource = null): StreamInterface
    {
        if (self::$stream_factory === null) {
            self::$stream_factory = new Factory\StreamFactory();
        }

        return self::$stream_factory->createStream($resource);
    }

    /**
     * Create a stream from an existing file.
     *
     */
    public function createStreamFromFile($filename, $mode = 'r')
    {
        if (self::$stream_factory === null) {
            self::$stream_factory = new Factory\StreamFactory();
        }

        return self::$stream_factory->createStreamFromFile($filename, $mode);
    }

    /**
     * Create a new stream from an existing resource.
     *
     * The stream MUST be readable and may be writable.
     *
     * @param resource $resource
     *
     * @return StreamInterface
     */
    public function createStreamFromResource($resource)
    {
        if (self::$stream_factory === null) {
            self::$stream_factory = new Factory\StreamFactory();
        }

        return self::$stream_factory->createStreamFromResource($resource);
    }

    /**
     * Creates a UploadedFile instance.
     */
    public static function createUploadedFile(
        $file,
        $size = null,
        $error = \UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ): UploadedFile
    {
        if (self::$uploaded_file_factory === null) {
            self::$uploaded_file_factory = new Factory\UploadedFileFactory();
        }

        return self::$uploaded_file_factory->createUploadedFile($file);
    }

    /**
     * Creates a Uri instance.
     */
    public static function createUri(
        string $uri = ''
    ): UriInterface
    {
        if (self::$uri_factory === null) {
            self::$uri_factory = new Factory\UriFactory();
        }

        return self::$uri_factory->createUri($uri);
    }
}
