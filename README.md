# LazyFocusFit

A SilverStripe module with template methods to quickly make use of [FocusPoint](https://github.com/jonom/silverstripe-focuspoint), [LazySizes](https://github.com/aFarkas/lazysizes) and [object-fit](https://developer.mozilla.org/en-US/docs/Web/CSS/object-fit).

## Requirements 

**PHP**

* [FocusPoint](https://github.com/jonom/silverstripe-focuspoint)

**JS/CSS**

* [LazySizes](https://github.com/aFarkas/lazysizes)
* [LazySizes bgset extension](https://github.com/aFarkas/lazysizes/tree/gh-pages/plugins/bgset)
* [object-fit](https://developer.mozilla.org/en-US/docs/Web/CSS/object-fit) compatible browser, OR
* [objectFitPolyfill](https://github.com/constancecchen/object-fit-polyfill)

## Installation

`composer require evanshunt/lazy-focus-fit`

After installing this module, ensure front-end requirements are installed and properly initiated within your project.

## Usage

This module adds 3 primary methods to SilverStripe `Image` objects, to allow quick creation of responsive image markup eather as `<img>` or `<picture>` tags or attributes which can be applied to any element for use as a background image.

Additional methods allow configuring the images to use object-fit, crop according to a specific aspect ratio, or add additional html attributes.

### ResponsiveImg()

This method allows generating an `<img>` tag with a number of possible image sizes. On page load a 32x32px image will be loaded and the correct size will be lazyloaded using LazySizes auto-sizes functionality. A future version may allow explicitly defining a sizes attribute or using the browser's built in responsive sizing w/o LazySizes.

**Example**

```$Image.ResponsiveImg(classname, 1200, 800, 600)```

### ResponsivePicture()

This method generateds a `<picture>` element, and allows for more explicit art direction than `ResponsiveImg()`. You can define media query conditions under which your images will be shown. After the first classname argument, subsequent arguments should take the following format:

`{ImageWidth}-{WidthDescriptor}-{PixelDensityDescriptor} {MinWidth}`

Width and Pixel density descriptors are optional as defined here: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/source#Attributes

MinWidth should be a screen width for a (min-width: xxx) media query, or 'default'. Start with largest width and work your way down to default.

**Examples**

```
$Image.ResponsivePicture(classname, 770-1x 1440-2x 992px, 496-1x 992-2x default)
$Image.ResponsivePicture(classname, 770-770w 992px, 496-496w default)
$Image.ResponsivePicture(classname, 770 992px, 496 default)
```

### ResponsiveBgAttributes

This method generates html attributes, not an entire element, it is used to apply a background image. The arguments operate the same way as `ResponsivePicture()`

Be aware that this method generates `class` and `style` attributes so adding these to your template code will result in dublicated attributes.

**Example**

```
<div $Image.ResponsiveBgAttributes(classname, 770-1x 1440-2x 992px, 496-1x 992-2x default)>
    ...
</div>
```

### Helper methods

#### AddAttribute()

Adds additional attribute to the `<picture>` element generated by `ResponsivePicture()` or the `<img>` attribute generated by `ResponsiveImg()`. Must be called before the markup generating method.

**Example**

```
$Image.AddAttribute(aria-hidden, true).ResponsivePicture(classname, 770 992px, 496 default). 
```

#### AddImgAttribute()

Adds additoinal attribute to the `<img>` element w/in the `<picture>` generated by `ResponsivePicture()`. Must be called before the markup generating method.

**Example**

```
$Image.AddImgAttribute(data-foo, bar).ResponsivePicture(classname, 770 992px, 496 default)
```

#### AddAspectRatio()

Crops image to a specific proportion, centered on the FocusPoint, for use with `ResponsivePicture()` and `ResponsiveImg()`. Must be called before the markup generating method.

**Example**

```
$Image.AddAspectRatio(6, 9).ResponsivePicture(classname, 770 992px, 496 default)
```

#### ObjectFit()

Adds object-fit styles to style tag of `ResponsivePicture()` and `ResponsiveImg()`, also adds necessary data-attributes to work with [objectFitPolyfill](https://github.com/constancecchen/object-fit-polyfill). Object position values come from FocusPoint.

By default `object-fit: cover;` is applied, but alternative values (fill, contain, scale-down) can be passed as an argument.

**Examples**

```
$Image.ObjectFit().ResponsivePicture(classname, 770 992px, 496 default)
$Image.ObjectFit(contain).ResponsivePicture(classname, 770 992px, 496 default)
```
