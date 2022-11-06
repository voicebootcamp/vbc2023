<?php

namespace QuixNxt\Image\Scaler;

use Intervention\Image\Image;
use Symfony\Component\Finder\SplFileInfo;

class SizesScaler extends AbstractScaler
{
    /**
     * @var array
     */
    private $sizes = [];

    /**
     * Responsive breakpoints.
     *
     * @var array
     */
    protected $responsiveBreakPoints = [
        1900 => 'large_desktop',
        1400 => 'desktop',
        1024 => 'tablet',
        768 => 'mobile',
        400 => 'mini'
    ];

    /**
     * @param array $sizes
     *
     * @return SizesScaler
     */
    public function setSizes(array $sizes) : SizesScaler
    {
        $this->sizes = $sizes;

        return $this;
    }

    /**
     * @param SplFileInfo $sourceFile
     * @param Image       $imageObject
     *
     * @return array
     */
    public function scale(SplFileInfo $sourceFile, Image $imageObject) : array
    {
        $imageWidth = $imageObject->getWidth();
        $imageHeight = $imageObject->getHeight();
        $ratio = $imageHeight / $imageWidth;

        $sizes = [];
        $responsiveBreakPoints = [];

        if ($this->includeSource) {
            $sizes[$imageWidth] = $imageHeight;
        }

        foreach ($this->sizes as $key => $width) {
            if ($width > $imageWidth) {
                continue;
            }
            $responsiveBreakPoints[$width] = $this->responsiveBreakPoints[$key];

            $sizes[$width] = round($width * $ratio);
        }

        return [$sizes, $responsiveBreakPoints];
    }
}
