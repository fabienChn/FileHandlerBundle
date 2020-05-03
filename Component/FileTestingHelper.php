<?php

namespace fabienChn\FileHandlerBundle\Component;

use PHPUnit\Framework\Exception;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Debug\Exception\ErrorException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * class FileTestingHelper
 * @package fabienChn\FileHandlerBundle\Component
 */
class FileTestingHelper
{
    /**
     * @var
     */
    private $tempFile;

    /**
     * @var array
     */
    private $config;

    /**
     * FileTestingHelper constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param $function
     * @return mixed
     */
    private function fileSystemManagement($function)
    {
        try {
            return $function(new Filesystem());
        } catch (IOExceptionInterface $e) {
            $info = [
                'message' => $e->getMessage(),
                'path' => $e->getPath()
            ];

            throw new Exception(implode(", ", $info));
        }
    }

    /**
     * Delete all folders and files in web/uploads and web/temp and rebuild them empty
     */
    public function cleanDummyFiles(): void
    {
        $this->fileSystemManagement(function (Filesystem $fs) {
            $uploadsFolderPath = $this->config['upload_folder'];
            $tempFolderPath = $this->config['temp_folder'];

            $fs->remove($uploadsFolderPath);
            $fs->mkdir($uploadsFolderPath, 0755, true);
            $fs->mkdir($tempFolderPath, 0755, true);
        });

        try {
            if ($this->tempFile && (new Filesystem())->exists($this->tempFile)) {
                unlink($this->tempFile);
            }
        } catch (ContextErrorException $e) {
        }
    }

    /**
     * @return mixed
     */
    public function getTempFile()
    {
        return $this->tempFile;
    }

    /**
     * @return void
     */
    private function generateTempFile(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'fixture-'); // create file
    }

    /**
     * @param string $format
     * @param int $height
     * @param int|null $width
     * @return string
     * @throws \Exception
     */
    public function generateImage(string $format, int $height = 10, int $width = null): string
    {
        $this->generateTempFile();

        $function = 'image'.($format == 'jpg' ? 'jpeg' : $format);

        $width = $width ?? $height; // if no with given, make a square

        $emptyImage = imagecreatetruecolor($height, $width);

        $function($emptyImage, $this->tempFile); // create and write image/{$format} to it

        imagedestroy($emptyImage);

        $this->renameTempFile($this->tempFile . '.' .$format);

        return $this->tempFile;
    }

    /**
     * @param string|null $fileName
     * @param string $format
     * @return UploadedFile
     */
    public function generateImageUploadedFile(string $format, string $fileName = null): UploadedFile
    {
        $this->generateImage($format);

        return $this->generateUploadedFileFromTempFile($fileName);
    }

    /**
     * @param string|null $fileName
     * @return UploadedFile
     */
    public function generateUploadedFileFromTempFile(string $fileName = null): UploadedFile
    {
        $format = strtolower(pathinfo($this->tempFile, PATHINFO_EXTENSION));

        $fileName = $fileName ?? (basename($this->tempFile).'.'.$format);

        $mimeType = 'image/'.($format == 'jpg' ? 'jpeg' : $format);

        return new UploadedFile($this->tempFile, $fileName, $mimeType);
    }

    /**
     * @param string|null $fileName
     * @return UploadedFile
     */
    public function generateTextUploadedFile(string $fileName = null): UploadedFile
    {
        $this->generateTempFile();

        $this->renameTempFile($this->tempFile.'.txt');

        return $this->generateUploadedFileFromTempFile($fileName);
    }

    /**
     * @param string $newName
     * @throws \Exception
     */
    private function renameTempFile(string $newName): void
    {
        if (rename($this->tempFile, $newName)) {
            $this->tempFile = $newName;
        } else {
            throw new \Exception("File couldn't be renamed");
        }
    }
}
