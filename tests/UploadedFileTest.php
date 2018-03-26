<?php

use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Engine\Http\UploadedFile;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UploadedFileInterface;

use PHPUnit\Framework\TestCase;

class UploadedFileTest extends TestCase
{
    protected function createTemporaryFile($content = null)
    {
        $file = tempnam(sys_get_temp_dir(), uniqid());
        if ($content) {
            $resource = fopen($file, 'r+');
            fwrite($resource, $content);
            rewind($resource);
        }

        return $file;
    }

    protected function assertUploadedFile(
        $file,
        $content,
        $size = null,
        $error = null,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertSame($content, (string) $file->getStream());
        $this->assertSame($size ?: strlen($content), $file->getSize());
        $this->assertSame($error ?: UPLOAD_ERR_OK, $file->getError());
        $this->assertSame($clientFilename, $file->getClientFilename());
        $this->assertSame($clientMediaType, $file->getClientMediaType());
    }

    public function testCreateUploadedFileWithString()
    {
        $content = 'i made this!';
        $size = strlen($content);
        $filename = $this->createTemporaryFile();

        file_put_contents($filename, $content);

        $file = new UploadedFile($filename);
        $this->assertUploadedFile($file, $content, $size);
    }

    public function testCreateUploadedFileWithClientFilenameAndMediaType()
    {
        $content = 'this is your capitan speaking';
        $upload = $this->createTemporaryFile($content);
        $error = UPLOAD_ERR_OK;
        $clientFilename = 'test.txt';
        $clientMediaType = 'text/plain';
        $size = strlen($content);

        $file = new UploadedFile($upload, $clientFilename, $clientMediaType, $size, $error);
        $this->assertUploadedFile($file, $content, null, $error, $clientFilename, $clientMediaType);
    }

    public function testCreateUploadedFileWithError()
    {
        $error = UPLOAD_ERR_NO_FILE;
        $file = new UploadedFile(null);

        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertEquals($error, $file->getError());
    }
}
