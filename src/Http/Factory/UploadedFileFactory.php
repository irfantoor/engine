<?php

namespace IrfanTOOR\Engine\Http\Factory;

use Interop\Http\Factory\UploadedFileFactoryInterface;
use IrfanTOOR\Engine\Http\UploadedFile;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * Create a new uploaded file.
     *
     * If a string is used to create the file, a temporary resource will be
     * created with the content of the string.
     *
     * If a size is not provided it will be determined by checking the size of
     * the file.
     *
     * @see http://php.net/manual/features.file-upload.post-method.php
     * @see http://php.net/manual/features.file-upload.errors.php
     *
     * @param string|resource $file
     * @param integer $size in bytes
     * @param integer $error PHP file upload error
     * @param string $clientFilename
     * @param string $clientMediaType
     *
     * @return UploadedFileInterface
     *
     * @throws \InvalidArgumentException
     *  If the file resource is not readable.
     */
    public function createUploadedFile(
        $file,
        $size = null,
        $error = \UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ) : UploadedFileInterface
    {
        return new UploadedFile([
            'name' => $file,
            'size' => $size,
            'error' => $error,
            'name' => $clientFilename,
            'type' => $clientMediaType,
        ]);
    }


    public static function createFromEnvironment()
    {
        if ($_FILES)
            return self::_parse($_FILES);
        else
            return [];
    }

    private function _parse(array $files)
    {
        $uploaded = [];
        foreach ($files as $id => $file) {
            if (!isset($file['error'])) {
                continue;
            }

            $uploaded[$id] = [];
            if (!is_array($file['error'])) {
                $uploaded[$id] = new static(
                    $file['tmp_name'],
                    isset($file['name']) ? $file['name'] : null,
                    isset($file['type']) ? $file['type'] : null,
                    isset($file['size']) ? $file['size'] : null,
                    $file['error'],
                    true
                );
            } else {
                $subArray = [];
                foreach ($file['error'] as $fileIdx => $error) {
                    // normalise subarray and re-parse to move the input's keyname up a level
                    $subArray[$fileIdx]['name'] = $file['name'][$fileIdx];
                    $subArray[$fileIdx]['type'] = $file['type'][$fileIdx];
                    $subArray[$fileIdx]['tmp_name'] = $file['tmp_name'][$fileIdx];
                    $subArray[$fileIdx]['error'] = $file['error'][$fileIdx];
                    $subArray[$fileIdx]['size'] = $file['size'][$fileIdx];

                    $uploaded[$id] = static::parseUploadedFiles($subArray);
                }
            }
        }

        return $uploaded;
    }
}
