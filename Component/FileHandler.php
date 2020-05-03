<?php

namespace fabienChn\FileHandlerBundle\Component;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ImageHandler
 * @package fabienChn\FileHandlerBundle\Component
 */
class FileHandler
{
    use ImageHandlingHelpersTrait;

    /**
     * If you created a new context, please add it in this array as well
     *
     * @var array
     */
    const AVAILABLE_CONTEXTS = [
        'avatar'
    ];

    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    private $uploadsFolder;

    /**
     * ImageHandler constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->uploadsFolder = $config['upload_folder'];
    }

    /**
     * @param File $file
     * @return self
     */
    public function setFile(File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param string $context
     * @return self
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function moveToUploads(string $context): self
    {
        if (! in_array($context, self::AVAILABLE_CONTEXTS)) {
            throw new \UnexpectedValueException("Unknow context \"$context\"");
        }

        $folder = $this->uploadsFolder . '/' . $context;

        if (!is_dir($folder)) {
            if (mkdir($folder, 0755, true) === false) {
                throw \RuntimeException("Folder \"$folder\" could not be created");
            }
        }

        $this->file = $this->file->move($folder, $this->file->getFilename());

        return $this;
    }

    /**
     * e.g.: if file is 'image.png', returns 'image';
     *
     * @return string
     */
    public function getFileName(): string
    {
        return basename(
            $this->file->getFilename(),
            '.' . $this->file->getExtension()
        );
    }
}
