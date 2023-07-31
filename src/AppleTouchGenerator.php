<?php

declare(strict_types=1);

namespace Genkgo\Favicon;

final class AppleTouchGenerator implements GeneratorInterface
{
    public function __construct(
        private readonly Input $input,
        private readonly int $size,
        private readonly string $backgroundColor = 'transparent',
    ) {
    }

    public function generate(): string
    {
        $imagick = new \Imagick();
        $backgroundPixel = new \ImagickPixel($this->backgroundColor);

        $imagick->setBackgroundColor($backgroundPixel);
        $imagick->readImageFile($this->input->rewindedFileHandle());
        $imagick->trimImage(0);

        $newWidth = $imagick->getImageWidth();
        $newHeight = $imagick->getImageHeight();

        $padding = (int)(0.1 * $newWidth);
        $composite = new \Imagick();
        $composite->newImage($newWidth + 2 * $padding, $newHeight + 2 * $padding, new \ImagickPixel('none'));

        $paddingDraw = new \ImagickDraw();
        $paddingDraw->setFillColor($backgroundPixel);
        $paddingDraw->roundRectangle(
            0,
            0,
            $newWidth + 2 * $padding,
            $newHeight + 2 * $padding,
            $padding,
            $padding
        );
        $composite->drawImage($paddingDraw);
        $composite->setImageFormat('png');
        $composite->compositeImage(
            $imagick,
            \Imagick::COMPOSITE_DEFAULT,
            $padding,
            $padding,
        );

        $composite->scaleImage($this->size, $this->size);
        $composite->setFormat('png');
        $composite->setImageFormat('png');
        $composite->setCompression(\Imagick::COMPRESSION_ZIP);
        $composite->setImageCompression(\Imagick::COMPRESSION_ZIP);

        return $composite->getImagesBlob();
    }
}
