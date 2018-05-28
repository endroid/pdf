# PDF

*By [endroid](https://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![Build Status](http://img.shields.io/travis/endroid/pdf.svg)](http://travis-ci.org/endroid/pdf)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![License](http://img.shields.io/packagist/l/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)

Library for easy PDF generation built around wkhtmltopdf and Snappy.

## Easy data loading

When you generate a PDF you need to make sure you pass the right contents to
the PDF object. This data can come from any source (i.e. a file, a URL, a controller)
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
    ])
;
```

For more information [read the documentation](https://github.com/endroid/asset).

## Efficient data loading

An HTML page can contain a number of external resources, each triggering a
separate request. However during PDF generation this can lead to performance or
even stability issues. Therefor we need the number of requests to be as low as
possible.

The [endroid/embed](https://github.com/endroid/embed) library helps you
minimize the number of assets to load during PDF generation by allowing you to
embed external resources via a Twig extension. You can use this extension to
embed resources like fonts, stylesheets, scripts etc.

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

For more information [read the documentation](https://github.com/endroid/embed).

## Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require endroid/pdf
```

Automatic framework configuration is provided by
[endroid/installer](https://github.com/endroid/installer). Please note that by
default all asset types are installed. If any of the asset types is unsupported
(because you miss a required service) you can uncomment the adapter in the
service configuration.

## Usage

If [endroid/installer](https://github.com/endroid/installer) detects Symfony
the builder is already autowired and you only need to provide the correct type
hint to retrieve the builder. Now you can use it like this.

```php
$pdfBuilder
    ->setCover([
        'controller' => CoverController::class,
        'cache_key' => 'cover',
        'cache_expires_after' => 3600,
    ])
    ->setTableOfContents([
        'path' => '/var/www/html/table_of_contents',
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

## Custom bootstrapping

When no autowiring is available you can initialize the builder like this.
Make sure you do this only once and create a service you can reuse.

```php
$snappy = new Snappy(__DIR__.'/../vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');

// done automatically when using autowiring
$assetFactory = new AssetFactory();
$assetFactory->add(new DataAssetFactoryAdapter());
$assetFactory->add(new ControllerAssetFactoryAdapter($kernel, $requestStack));
$assetFactory->add(new TemplateAssetFactoryAdapter($twig));
... // depending on what services you have available

$pdfBuilder = new PdfBuilder(new Pdf($snappy), $assetFactory);
```

## Versioning

Version numbers follow the MAJOR.MINOR.PATCH scheme. Backwards compatible
changes will be kept to a minimum but be aware that these can occur. Lock
your dependencies for production and test your code when upgrading.

## License

This bundle is under the MIT license. For the full copyright and license
information please view the LICENSE file that was distributed with this source code.
