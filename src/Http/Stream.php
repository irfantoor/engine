<?php

namespace IrfanTOOR\Engine\Http;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Stream\Stream as GStream;
use IrfanTOOR\Engine\Exception;

/**
 * Describes a data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 */
class Stream extends GStream implements StreamInterface
{

    public static function createFromString($contents, $options = [])
    {
        $mode = $options['metadata']['mode'] ?: 'r+';
        $stream = fopen('php://temp', $mode);
        if ($contents !== '') {
            fwrite($stream, $contents);
            fseek($stream, 0);
        }
        return new static($stream, $options);
    }

    public static function createFromFile($file, $options = [])
    {
        $mode = $options['metadata']['mode'] ?: 'r';
        $stream = fopen($file, $mode);
        return new static($stream, $options);
    }

    /**
     * This constructor accepts an associative array of options.
     *
     * - size: (int) If a read stream would otherwise have an indeterminate
     *   size, but the size is known due to foreknownledge, then you can
     *   provide that size, in bytes.
     * - metadata: (array) Any additional metadata to return when the metadata
     *   of the stream is accessed.
     *
     * @param resource $stream  Stream resource to wrap.
     * @param array    $options Associative array of options.
     *
     * @throws \InvalidArgumentException if the stream is not a stream resource
     */
    public function __construct($stream, $options = null)
    {
        if (null === $options)
            $options = [
                'metadata' => [
                    'mode' => 'r+'
                ]
            ];

        parent::__construct($stream, $options);
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        if (!$this->isSeekable())
            throw new Exception('this stream is not seekable');

        $this->seek(0);
    }
}
