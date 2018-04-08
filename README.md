# PDF

*By [endroid](https://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![Build Status](http://img.shields.io/travis/endroid/pdf.svg)](http://travis-ci.org/endroid/pdf)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![License](http://img.shields.io/packagist/l/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)

Library for easy PDF generation built around wkhtmltopdf and Snappy.

## Features

* Use any type of source: a HTML string, a file, a template or a controller.
* Add a cover, table of contents, header, footer or contents from any source.
* Embed resources like fonts, images, stylesheets and scripts.
* Cache resources to improve performance.

## Example usage

```php

$pdfBuilder
    ->setCover(['controller' => CoverController::class])
    ->setTableOfContents(['template' => 'pdf/table_of_contents.xml.twig', 'cache' => 'toc'])
    ->setHeader(['file' => 'header.html'])
    ->setFooter(['template' => 'pdf/footer.html.twig', 'cache' => 'footer'])
    ->setContent(['controller' => ContentController::class])
    ->setOptions([
        'margin-top' => 16,
        'margin-bottom' => 16,
        'header-spacing' => 5,
        'footer-spacing' => 5,
    ])
;

$pdf = $pdfBuilder->getPdf();
$pdf->save(''
```

## Loading content

The PDF builder takes any of the following asset types as a data source.

* DataAsset: the most basic form: contains only a string holding the text or HTML.
* FileAsset: loads contents from a given file name.
* TemplateAsset: loads contents from a Twig template and optional parameters.
* ControllerAsset: loads contents from a controller action and optional parameters.
* CachedAsset: caches contents by wrapping any of the above assets.

The asset factory

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require endroid/pdf
```

Now you can create the PDF builder as follows.

```php
$snappy = new Snappy(__DIR__.'/../vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
$pdfBuilder = new PdfBuilder(new Pdf($snappy), new AssetFactory());
```

If you use Symfony Flex the builder is already autowired and ready for injection.

```php
public function __construct(PdfBuilder $pdfBuilder)
{
    $this->pdfBuilder = $pdfBuilder;
}
```

## Components

## PDF



## PDF Builder



## Asset Factory

Each source of content, like the cover, the header / footer or the contents of
the PDF can be rendered from different sources. You can specify a controller
action, render a template, load a file or use a plain old string as source.

Some assets require external dependencies. For instance, to render a controller
action you need a kernel, to render a template you need a templating engine and
to create a cached asset you need a cache adapter. The factory lets you create
assets of any type without worrying of additional dependencies.

## Twig Extension

PDF Generators have a bad reputation in resolving URLs like for fonts, style
sheets, scripts or images. This is mainly caused by the fact that PDF generation
is executed as a process that has no knowledge of URLs but only looks at internal
file paths. Also, header and footer files are often loaded multiple times (once
for each page) so we don't want external resources to be fetched every time.

The library comes with a Twig extension that you can use to embed such resources.
Whether you have a font, an image or an external stylesheet, you can can use the
twig extension to embed it as a base64 encoded string.

```php
<link rel="stylesheet" href="{{ embed('http://hostname/styles.css') }}">

<style>
@font-face {
    font-family: 'SCP';
    font-weight: normal;
    src: url('{{ embed('https://fontlibrary.org/scp.ttf') }}');
}
.cover {
    background-image: url('{{ embed('http://pipsum.com/793x1122.jpg') }}');
    width: 793px;
    height: 1072px;
    padding-top: 50px;
}
</style>
```

## Versioning

Version numbers follow the MAJOR.MINOR.PATCH scheme. Backwards compatible
changes will be kept to a minimum but be aware that these can occur. Lock
your dependencies for production and test your code when upgrading.

## License

This bundle is under the MIT license. For the full copyright and license
information please view the LICENSE file that was distributed with this source code.
