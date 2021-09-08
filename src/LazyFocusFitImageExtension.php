<?php

namespace EvansHunt\LazyFocusFit;

use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;

class LazyFocusFitImageExtension extends DataExtension
{
    private $pictureClassString;
    private $pictureSrcArray;
    private $autoSizesBoolean;

    public $IsObjectFit = false;
    public $AttributeString;
    public $ImgAttributeString;
    public $AspectRatio;
    public $IsNaturalWidth = false;
    public $ImgSrcset = '';
    public $ImgSrc = '';

    private static $casting = [
        'AttributeString' => 'HTMLVarchar',
        'ImgAttributeString' => 'HTMLVarchar'
    ];

    /**
     * ResponsivePicture - generates a <picture> element with lazyloaded responsive sizing
     *
     * First argument should be className
     * All subsequent arguments should be '{ImageWidth}-{WidthDescriptor}-{PixelDensityDescriptor} {MinWidth}`
     *
     * Width and Pixel density descriptors are optional as defined here:
     * https://developer.mozilla.org/en-US/docs/Web/HTML/Element/source#Attributes
     *
     * MinWidth should be a screen width for a (min-width: xxx) media query, or 'default'
     * Start with largest width and work your way down to default
     *
     * Example:
     * $Image.ResponsivePicture(classname, 770-1x 1440-2x 992px, 496-1x 992-2x default)
     */

    public function ResponsivePicture(...$arguments)
    {
        if (!$this->owner->Exists()) {
            return null;
        }

        $this->populateResponsiveImageAttributes($arguments);

        return $this->owner->renderWith('EvansHunt\\LazyFocusFit\\Picture');
    }

    /**
     * ResponsiveImg - generates a <img> element with lazyloaded responsive sizing
     *
     * First argument should be className
     * All subsequent arguments should be image widths in pixels
     * Lazysizes will insert the correct image according to the rendered size
     *
     * Example:
     * $Image.ResponsivePicture(classname, 800, 600, 300)
     */

    public function ResponsiveImg(...$arguments)
    {
        if (!$this->owner->Exists()) {
            return null;
        }

        $srcsetArray = [];

        $this->pictureClassString = array_shift($arguments);

        foreach ($arguments as $argument) {
            $sourceItems = explode(' ', $argument);
            $size = array_shift($sourceItems);

            if (is_numeric($size)) {
                $image = $this->croppedImage($size);
                $srcsetArray[] =  $image->URL . ' ' . $image->Width . 'w';
            }
        }

        $this->owner->ImgSrcset = implode(", ", $srcsetArray);

        return $this->owner->renderWith('EvansHunt\\LazyFocusFit\\Img');
    }

    /**
     * ResponsiveBgAttributes - generates html attributes element with a lazyloaded responsive image background
     *
     * First argument should be className
     * All subsequent arguments should be '{ImageWidth}-{WidthDescriptor}-{PixelDensityDescriptor} {MinWidth}`
     *
     * Width and Pixel density descriptors are optional as defined here:
     * https://developer.mozilla.org/en-US/docs/Web/HTML/Element/source#Attributes
     *
     * MinWidth should be a screen width for a (min-width: xxx) media query, or 'default'
     * Start with largest width and work your way down to default
     *
     * Example:
     * <div $Image.ResponsiveBgAttributes(classname, 770-1x 1440-2x 992px, 496-1x 992-2x default)>
     *     ...
     * </div>
     */

    public function ResponsiveBgAttributes(...$arguments)
    {
        $this->populateResponsiveImageAttributes($arguments);

        return $this->owner->renderWith('EvansHunt\\LazyFocusFit\\BackgroundAttributes');
    }

    private function populateResponsiveImageAttributes($arguments)
    {
        $this->pictureClassString = array_shift($arguments);
        $this->pictureSrcArray = array_map(function ($source) {
            $sourceItems = explode(' ', $source);
            $minWidth = array_pop($sourceItems);
            $minWidth = $minWidth === 'default' ? '' : $minWidth;

            return [
                'Sizes' => $this->owner->exists() ? new ArrayList(array_map(function ($item) {
                    $params = explode('-', $item);
                    $size = array_shift($params);
                    $image = $this->croppedImage($size);

                    return [
                        'URL' => $image ? $image->URL : '',
                        'Descriptors' => join(' ', $params)
                    ];
                }, $sourceItems)) : [],
                'MinWidth' => $minWidth,
                'AutoSizes' => array_reduce($sourceItems, function ($carry, $item) {
                    return $carry || strpos($item, 'w') !== false;
                })
            ];
        }, $arguments);

        $this->autoSizesBoolean = array_reduce($this->pictureSrcArray, function ($carry, $item) {
            return $carry || $item['AutoSizes'];
        });
    }

    public function PictureClass()
    {
        return $this->pictureClassString;
    }

    public function PictureSources()
    {
        return new ArrayList($this->pictureSrcArray);
    }

    // If using a value other than image title for alt tag
    // logic can be added here
    // @Todo: add extension point
    public function Caption()
    {
        return $this->owner->Title;
    }

    public function TinySrc()
    {
        $image = $this->croppedImage(32);

        return $image ? $image->URL : '';
    }

    private function croppedImage($size)
    {
        if ($this->owner->exists()) {
            $originalRatio = $this->owner->Width / $this->owner->Height;

            if ($this->owner->AspectRatio) {
                if ($this->owner->AspectRatio > $originalRatio) {
                    return $this->owner
                        ->Resampled()
                        ->FocusCropHeight(round($this->owner->Width / $this->owner->AspectRatio))
                        ->ScaleMaxWidth($size);
                }
                return $this->owner
                    ->Resampled()
                    ->FocusCropWidth(round($this->owner->Height * $this->owner->AspectRatio))
                    ->ScaleMaxWidth($size);
            }
        }

        return $this->owner->Resampled()->ScaleMaxWidth($size);
    }

    public function AutoSizes()
    {
        return $this->autoSizesBoolean;
    }

    // Adds any attribute to the <picture> element returned by ResponsivePicture()
    public function AddAttribute($name, $value = null)
    {
        $this->owner->AttributeString .= $this->owner->AttributeString ? ' ' : '';
        $this->owner->AttributeString .= $name . ($value ? '="' . $value . '"' : '');

        return $this->owner;
    }

    // Adds any attribute to the <img> element returned by ResponsivePicture()
    public function AddImgAttribute($name, $value = null)
    {
        $this->owner->ImgAttributeString .= $this->owner->ImgAttributeString ? ' ' : '';
        $this->owner->ImgAttributeString .= $name . ($value ? '="' . $value . '"' : '');

        return $this->owner;
    }

    // Allows specifying an aspect ratio when using ResponsivePicture()
    public function AddAspectRatio($width, $height)
    {
        $this->owner->AspectRatio = $width / $height;
        return $this->owner;
    }

    // Allows using object-fit and object-cover css properties, w/ IE polyfill and FocusPoint integration
    public function ObjectFit($objectFitType = 'cover')
    {
        $this->owner->IsObjectFit = true;
        $this->owner->ObjectFitType = $objectFitType;
        return $this->owner;
    }

    public function NaturalWidth()
    {
        $this->owner->IsNaturalWidth = true;
        return $this->owner;
    }

    // Original width will be used for html width attribute
    // Height attribute is width/aspect ratio
    public function CroppedHeight()
    {
        return $this->owner->AspectRatio
            ? round($this->owner->Width / $this->owner->AspectRatio)
            : $this->owner->Height;
    }
}
