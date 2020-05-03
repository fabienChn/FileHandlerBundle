<?php

namespace fabienChn\FileHandlerBundle\Component;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Trait ImageHandlingHelpersTrait
 * @package fabienChn\FileHandlerBundle\Component
 */
trait ImageHandlingHelpersTrait
{
    /**
     * @param int $finalSize
     * @return $this
     * @throws \Exception
     */
    public function crop($finalSize = 200): self
    {
        if (! in_array(strtolower($this->file->getExtension()), ['png', 'gif', 'jpg', 'jpeg'])) {
            throw new \Exception("You can't crop a file if it's not an image");
        }

        $size = getimagesize($this->file->getRealPath());

        // in square
        $sideSize = min($size[0], $size[1]);

        switch ($this->file->getExtension()) {
            case 'png':
                $oldImage = \imagecreatefrompng($this->file->getRealPath());
                break;
            case 'gif':
                $oldImage = \imagecreatefromgif($this->file->getRealPath());
                break;
            default:
                $oldImage = \imagecreatefromjpeg($this->file->getRealPath());
                break;
        }

        $croppedImage = imagecrop($oldImage, [
            'x' => ($size[0] - $sideSize) / 2,
            'y' => ($size[1] - $sideSize) / 2,
            'width' => $sideSize,
            'height' => $sideSize
        ]);

        $newImage = imagecreatetruecolor($finalSize, $finalSize);
        imagecopyresampled($newImage, $croppedImage, 0, 0, 0, 0, $finalSize, $finalSize, $sideSize, $sideSize);

        switch ($this->file->getExtension()) {
            case 'png':
                imagepng($newImage, $this->file->getRealPath());
                break;
            case 'gif':
                imagegif($newImage, $this->file->getRealPath());
                break;
            default:
                imagejpeg($newImage, $this->file->getRealPath());
                break;
        }

        imagedestroy($oldImage);
        imagedestroy($croppedImage);

        return $this;
    }

    /**
     * @param string $format
     * @return $this
     * @throws \Exception
     */
    public function convertImageTo(string $format): self
    {
        if (! in_array($format, ['png', 'jpeg'])) {
            throw new \Exception('Unsupported image format');
        }

        if ($this->file->getExtension() != $format) {
            $fileHavingAWrongExtension = $this->file->getRealPath();

            switch ($this->file->getExtension()) {
                case 'png':
                    $image = \imagecreatefrompng($fileHavingAWrongExtension);
                    break;
                case 'gif':
                    $image = \imagecreatefromgif($fileHavingAWrongExtension);
                    break;
                default:
                    $image = \imagecreatefromjpeg($fileHavingAWrongExtension);
                    break;
            }

            $function = 'image'.$format;

            $function($image, $this->file->getPath().'/'.$this->getFileName().'.'.$format);

            (new Filesystem())->remove($fileHavingAWrongExtension);
        }

        return $this;
    }
}
