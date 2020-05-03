<?php

namespace fabienChn\FileHandlerBundle\Component;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Uploader
 * @package fabienChn\FileHandlerBundle\Component
 */
class Uploader
{
    /**
     * Path to temp folder
     *
     * @var string
     */
    private $targetFolder;

    /**
     * File to upload
     *
     * @var File|UploadedFile
     */
    private $file;

    /**
     * Has the file been uploaded ?
     *
     * @var bool
     */
    private $uploaded;

    /**
     * Uploader constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->targetFolder = $config['temp_folder'];

        $this->uploaded = false;
    }

    /**
     * @param UploadedFile $file
     * @return Uploader
     */
    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return File
     * @throws \Exception
     */
    public function upload(): File
    {
        if (! isset($this->file)) {
            throw new \Exception('You need to set file before.');
        }

        $newFileName = uniqid().'.'.$this->file->getClientOriginalExtension();

        $this->file = $this->file->move($this->targetFolder, $newFileName);

        $this->uploaded = true;

        return $this->file;
    }
}
