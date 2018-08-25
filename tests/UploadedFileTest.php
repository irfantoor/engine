<?php

use IrfanTOOR\Engine\Http\Stream;
use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Engine\Http\UploadedFile;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UploadedFileInterface;

use PHPUnit\Framework\TestCase;

class UploadedFileTest extends TestCase
{

    function createTemporaryFile($contents = null)
    {
        $file = tempnam(sys_get_temp_dir(), uniqid());
        if ($contents) {
            file_put_contents($file, $contents);
        }
        return $file;
    }
    
    function assertUploadedFile(
        $file,
        $contents,
        $size = null,
        $error = null,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertSame($contents, (string) $file->getStream());
        $this->assertSame($size ?: strlen($contents), $file->getSize());
        $this->assertSame($error ?: UPLOAD_ERR_OK, $file->getError());
        $this->assertSame($clientFilename, $file->getClientFilename());
        $this->assertSame($clientMediaType, $file->getClientMediaType());
    }

    public function testCreateUploadedFileWithString()
    {
        $file = new UploadedFile(__FILE__);
        $contents = file_get_contents(__FILE__);
        $size = strlen($contents);
        
        $this->assertUploadedFile($file, $contents, $size);
    }

    public function testCreateUploadedFileWithClientFilenameAndMediaType()
    {
        $contents = 'this is your capitan speaking';
        $upload = $this->createTemporaryFile($contents);
        $error = UPLOAD_ERR_OK;
        $clientFilename = 'test.txt';
        $clientMediaType = 'text/plain';
        $size = strlen($contents);

        $file = new UploadedFile($upload, $clientFilename, $clientMediaType, $size, $error);
        $this->assertUploadedFile($file, $contents, null, $error, $clientFilename, $clientMediaType);
    }

    public function testCreateUploadedFileWithError()
    {
        $error = UPLOAD_ERR_NO_FILE;
        $file = new UploadedFile(null);

        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertEquals($error, $file->getError());
    }
}
