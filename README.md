# PDF

*By [endroid](https://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![Build Status](http://img.shields.io/travis/endroid/pdf.svg)](http://travis-ci.org/endroid/pdf)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![License](http://img.shields.io/packagist/l/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)

Library for easy PDF generation built around wkhtmltopdf and Snappy. Click
[here](https://endroid.nl/pdf) for an example. Please note this example takes
some time to load because it contains a lot of pages and no caching is applied.

Read the [blog](https://medium.com/@endroid/pdf-generation-in-symfony-3080702353b)
for more information on why I created this library and how to use it.

## Easy data loading

When you generate a PDF you need to make sure you pass the right contents to
the PDF object. This data can come from any source (a file, a URL, a controller)
and some of these impose a performance hit so you often want to cache some of
these contents instead of loading the data every time you generate the PDF.

The [endroid/asset](https://github.com/endroid/asset) takes this burden away by
allowing you to define your assets via a simple array of options. The asset
factory and guesser make sure the right type of asset is created and even
provide a so called cache asset that wraps any other asset.

```php
$this->pdfBuilder
    ->setCover([
        'controller' => CoverController::class,
        'parameters' => ['title' => 'My PDF', 'date' => new DateTime()],
        'cache_key' => 'cover',
        'cache_expires_after' => 3600,
        'cache_clear' => true, // use to purge any previously cached data
    ])
;
```

For more information [read the documentation](https://github.com/endroid/asset).

## Handling external resources

An HTML page can contain a number of external resources, each triggering a
separate request. However during PDF generation this can lead to performance or
even stability issues. Therefor we need the number of requests to be as low as
possible.

The [endroid/embed](https://github.com/endroid/embed) library helps you
minimize the number of assets to load during PDF generation by allowing you to
embed external resources via a Twig extension. You can use this extension to
embed resources like fonts, stylesheets and scripts.

```php
<link rel="stylesheet" href="{{ embed(asset('/styles.css')) }}">

<style>
@font-face {
    font-family: 'SCP';
    font-weight: normal;
    src: url('{{ embed('https://fontlibrary.org/scp.ttf') }}');
}
</style>
```

For more information you can [read the documentation](https://github.com/endroid/embed).

## The PDF builder

When [endroid/installer](https://github.com/endroid/installer) detects Symfony
the builder is automatically wired and you can immediately start using it to
build a PDF. This is an example of how you can use the builder.

```php
$pdfBuilder
    ->setCover([
        'controller' => CoverController::class,
        'cache_key' => 'cover',
        'cache_expires_after' => 3600,
    ])
    ->setTableOfContents([
        'path' => '/var/www/html/table_of_contents.xml',
        'cache_key' => 'toc',
    ])
    ->setHeader([
        'template' => 'pdf/header.html.twig',
        'cache_key' => 'header',
    ])
    ->setFooter([
        'template' => 'pdf/footer.html.twig',
        'cache_key' => 'footer',
    ])
    ->setContent([
        'url' => 'http://endroid.nl/',
        'cache_key' => 'content',
    ])
    ->setOptions([
        'margin-top' => 16,
        'margin-bottom' => 16,
        'header-spacing' => 5,
        'footer-spacing' => 5,
    ])
;

$pdf = $pdfBuilder->getPdf();

// Create a response object
$response = new InlinePdfResponse($pdf);

// Or output directly
header('Content-type: application/pdf');
echo $pdf->generate();
```

## Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require endroid/pdf
```

### Symfony

When you use Symfony, the [installer](https://github.com/endroid/installer)
makes sure that services are automatically wired. If the Snappy\Pdf service is
not registered yet, make sure you create a service definition for it or install
the knplabs/snappy-bundle along with the library.

``` bash
$ composer require endroid/pdf knplabs/snappy-bundle
```

Also, if any of the asset types is unsupported (for instance because you have
no cache component or Twig available) or if you simply don't want some to be
registered you can uncomment the adapter via the service configuration.

### Bootstrapping the PDF builder

When no autowiring is available you need to instantiate and wire the necessary
dependencies yourself. You can do so via a bootstrap file for instance.

```php
$snappy = new Snappy(__DIR__.'/../vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');

$assetFactory = new AssetFactory();
$assetFactory->add(new DataAssetFactoryAdapter());
$assetFactory->add(new ControllerAssetFactoryAdapter($kernel, $requestStack));
$assetFactory->add(new TemplateAssetFactoryAdapter($twig));
...

$pdfBuilder = new PdfBuilder(new Pdf($snappy), $assetFactory);
```

## Versioning

Version numbers follow the MAJOR.MINOR.PATCH scheme. Backwards compatible
changes will be kept to a minimum but be aware that these can occur. Lock
your dependencies for production and test your code when upgrading.

## License

This bundle is under the MIT license. For the full copyright and license
information please view the LICENSE file that was distributed with this source code.
