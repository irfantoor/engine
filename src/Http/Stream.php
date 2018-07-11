<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Engine\Exception;
use Psr\Http\Message\StreamInterface;

/**
 * Describes a data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 */
class Stream implements StreamInterface
{
    protected $stream   = null;
    protected $metadata = [];
    
    static function factory($mixed = null, $options = [])
    {
        if ($mixed === null) {
            $stream = new Stream(fopen('php://temp', 'w+'));
        } elseif (is_string($mixed)) {
            $stream = new Stream(fopen('php://temp', 'w+'));
            $stream->write($mixed);
            $stream->rewind();
        } elseif (is_resource($mixed)) {
            $stream = new Stream($mixed);
        } elseif (is_object($mixed)) {
            if (is_a($mixed, 'IrfanTOOR\\Engine\\Http\\Stream')) {
                $stream = $mixed;
            } elseif (is_a($mixed, 'ArrayIterator')) {
                $stream = new Stream(fopen('php://temp', 'w+'));
                foreach($mixed as $item) {
                    $stream->write($item);
                }
                $stream->rewind();
            } else {
                if (method_exists($mixed, '__toString')) {
                    $mixed = (string) $mixed;
                    $stream = new Stream(fopen('php://temp', 'w+'));
                    $stream->write($mixed);
                    $stream->rewind();
                } else {
                    throw new \InvalidArgumentException('Invalid argument mixed');
                }
            }
        }
        
        if (isset($options['metadata'])) {
            $md =  $options['metadata'];
            if (is_array($md)) {
                foreach($md as $k=>$v) {
                    $stream->setMetaData($k, $v);
                }
            }
        }
        
        if (isset($options['size'])) {
            $stream->setMetaData('size', $options['size']);
        }
        
        return $stream;
    }
    
    function __construct($stream)
    {
        if (is_resource($stream)) {
            $this->stream = $stream;
        } else {
            throw new \InvalidArgumentException('invalid argument stream');
        }
    }
    
    
    function __destruct()
    {
        $this->close();
    }
    
    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        $this->rewind();
        return $this->getContents();
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        # todo
        $stream = $this->stream;
        $this->stream = null;
        
        return $stream;
    }
    
    
    public function attach(&$stream)
    {
        $this->stream = $stream;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        return $this->getMetaData('size');
    }
    
    public function setSize($size)
    {
        $this->setMetaData('size', $size);
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        if (is_resource($this->stream)) {
            return ftell($this->stream);
        } else {
            return false;
        }
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        if ($this->stream)
            return feof($this->stream);
            
        return true;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        if (is_resource($this->stream)) {
            return true;
//             fseek($this->stream, 1, SEEK_CUR);
//             if (fseek($this->stream, -1, SEEK_CUR) === 0)
//                 return true;
        }
        return false;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if ($this->isSeekable()) {
            if (fseek($this->stream, $offset, $whence) !== 0) {
                throw new Exception('seek failure');
            }
            return true;
        }
        return false;
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
        if ($this->isSeekable()) {
            if (!rewind($this->stream)) {
                throw new Exception('rewind failure');
            }
            return true;
        }
        return false;
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        # todo
        return is_resource($this->stream) ? true : false;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if ($this->isWritable()) {
            return fwrite($this->stream, $string);
        } else {
            return false;
        }
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        # todo
        return is_resource($this->stream) ? true : false;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if ($this->isReadable()) {
            return fread($this->stream, $length);
        } else {
            return false;
        }
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        $contents = '';
        while (!$this->eof()) {
            $contents .= stream_get_contents($this->stream, 8192);
        }
        return $contents;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if (!is_resource($this->stream)) {
            return null;
        }
        
        $fstat = fstat($this->stream);
        $fstat = array_merge(array_slice($fstat, 13), $this->metadata);
        
        if ($key) {
            return isset($fstat[$key]) ? $fstat[$key] : null;
        }
        
        return $fstat;
    }
    
    function setMetaData($k, $v)
    {
        $this->metadata[$k] = $v;
    }
}
