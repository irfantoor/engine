<?php

namespace IrfanTOOR\Engine\Http\Factory;

use Interop\Http\Factory\StreamFactoryInterface;
use IrfanTOOR\Engine\Http\Stream;
use Psr\Http\Message\StreamInterface;

class StreamFactory implements StreamFactoryInterface
{
    /**
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param string $content
     *
     * @return StreamInterface
     */
    public function createStream($content = ''): StreamInterface
    {
        $stream = fopen('php://temp', 'r+');
        if ($content !== '') {
            fwrite($stream, $content);
            fseek($stream, 0);
        }

        return new Stream($stream, $options);
    }

    /**
     * Create a stream from an existing file.
     *
     * The file MUST be opened using the given mode, which may be any mode
     * supported by the `fopen` function.
     *
     * The `$filename` MAY be any string supported by `fopen()`.
     *
     * @param string $filename
     * @param string $mode
     *
     * @return StreamInterface
     */
    public function createStreamFromFile($filename, $mode = 'r')
    {
        if (file_exists($filename)) {
            $stream = fopen($filename, $mode);
            fseek($stream, 0);
        }

        return new Stream($stream, $options);
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
        return new Stream($resource, 'r+');
    }
}
