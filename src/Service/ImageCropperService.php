<?php

namespace App\Service;

class ImageCropperService
{
    private const COLOR_RANGE = [
        'min' => [
            'r' => 12,
            'g' => 55,
            'b' => 33,
        ],
        'max' => [
            'r' => 148,
            'g' => 166,
            'b' => 157,
        ],
    ];

    public function processImage(string $imagePath): string
    {
        $image = $this->createImageFromPath($imagePath);
        $dimensions = $this->getImageDimensions($image);
        $pixels = $this->getPixelsWithinColorRange($image, $dimensions);

        // find exceptions and remove them from the array
        $pixels = $this->removeExceptions($pixels);

        $cropRectangle = $this->determineCropRectangle($pixels);
        $croppedImage = $this->cropImage($image, $cropRectangle);

        $imagePath = $this->addFileSuffix($imagePath);
        $this->saveImage($croppedImage, $imagePath);

        return $imagePath;
    }

    private function createImageFromPath(string $imagePath): \GdImage
    {
        return imagecreatefromjpeg($imagePath);
    }

    private function getImageDimensions(\GdImage $image): array
    {
        return [
            'width' => imagesx($image),
            'height' => imagesy($image),
        ];
    }

    private function getPixelsWithinColorRange(\GdImage $image, array $dimensions): array
    {
        $pixels = [];
        ['min' => $min, 'max' => $max] = self::COLOR_RANGE;
        $redMax = $max['r'];
        $redMin = $min['r'];
        $greenMax = $max['g'];
        $greenMin = $min['g'];
        $blueMax = $max['b'];
        $blueMin = $min['b'];
        for ($x = 0; $x < $dimensions['width']; ++$x) {
            for ($y = 0; $y < $dimensions['height']; ++$y) {
                $colorIndex = imagecolorat($image, $x, $y);
                $color = imagecolorsforindex($image, $colorIndex);
                $r = $color['red'];
                $g = $color['green'];
                $b = $color['blue'];
                if ((($redMin <= $r) && ($r <= $redMax)) && (($greenMin <= $g) && ($g <= $greenMax)) && (($blueMin <= $b) && ($b <= $blueMax)) && (($r < $g) && ($b < $g))) {
                    $pixels[] = "$x/$y";
                }
            }
        }

        return $this->filterPixels($pixels);
    }

    private function filterPixels(array $pixels): array
    {
        return array_filter($pixels, function ($value) use ($pixels) {
            $val = explode('/', $value, 2);
            $x = $val[0];
            $y = $val[1];
            $valuesToCheck = [($x + 1).'/'.$y, ($x + 2).'/'.$y, $x.'/'.($y + 1), $x.'/'.($y + 2), ($x + 1).'/'.($y + 1), ($x + 2).'/'.($y + 2),
                ($x - 1).'/'.$y, ($x - 2).'/'.$y, $x.'/'.($y - 1), $x.'/'.($y - 2), ($x - 1).'/'.($y - 1), ($x - 2).'/'.($y - 2), ];

            $checks = [];
            foreach ($valuesToCheck as $value) {
                if (in_array($value, $pixels)) {
                    $checks[] = $value;
                }
                if (count($checks) >= 3) {
                    break;
                }
            }

            return count($checks) >= 3;
        });
    }

    private function hasNeighbour(array $pixels, string $pixel): bool
    {
        $pixelCoordinates = explode('/', $pixel);
        $x = $pixelCoordinates[0];
        $y = $pixelCoordinates[1];
        $neighbours = [
            "$x/$y",
            "$x/$y",
            "$x/$y",
            "$x/$y",
        ];

        return count(array_intersect($pixels, $neighbours)) > 0;
    }

    private function removeExceptions(array $pixels): array
    {
        $exceptions = $this->findExceptions($pixels);

        return array_diff($pixels, $exceptions);
    }

    private function findExceptions(array $pixels): array
    {
        $exceptions = [];
        foreach ($pixels as $pixel) {
            if (!$this->hasNeighbour($pixels, $pixel)) {
                $exceptions[] = $pixel;
            }
        }

        return $exceptions;
    }

    private function cropImage(\GdImage $image, array $cropRectangle): \GdImage
    {
        return imagecrop($image, $cropRectangle);
    }

    private function determineCropRectangle(array $pixels): array
    {
        $x = [];
        $y = [];
        foreach ($pixels as $pixel) {
            $pixelCoordinates = explode('/', $pixel, 2);
            $x[] = $pixelCoordinates[0];
            $y[] = $pixelCoordinates[1];
        }
        $xMin = min($x);
        $xMax = max($x);
        $yMin = min($y);
        $yMax = max($y);

        return [
            'x' => $xMin,
            'y' => $yMin,
            'width' => ($xMax - $xMin) + 5,
            'height' => ($yMax - $yMin) + 10,
        ];
    }

    private function saveImage(\GdImage $croppedImage, string $imagePath): void
    {
        imagejpeg($croppedImage, $imagePath);
    }

    private function addFileSuffix(string $imagePath): string
    {
        return preg_replace('/(\.[a-z]+)$/', '_cropped$1', $imagePath);
    }
}
