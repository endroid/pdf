# PDF

*By [endroid](https://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![Build Status](http://img.shields.io/travis/endroid/pdf.svg)](http://travis-ci.org/endroid/pdf)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![License](http://img.shields.io/packagist/l/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)

Library for easy PDF generation.

## Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require endroid/pdf
```

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

## Usage (in Symfony)

When installed with Flex, a recipe is executed that registers the Pdf class as
a service and automatically injects available services. For instance when
templating is available you can use Twig based assets and when an HTTP kernel
and request stack are available controller actions can be used as asset.

```php
<?php

namespace App\Controller\Pdf;

use Endroid\Pdf\Builder\PdfBuilder;
use Endroid\Pdf\Response\InlinePdfResponse;
use Symfony\Component\HttpFoundation\Response;

final class PdfController
{
    private $pdfBuilder;

    public function __construct(PdfBuilder $pdfBuilder)
    {
        $this->pdfBuilder = $pdfBuilder;
    }
    
    public function __invoke(): Response
    {
        $this->pdfBuilder
            ->setCover(['controller' => CoverController::class, 'cache' => 'cover'])
            ->setTableOfContents(['template' => 'pdf/table_of_contents.xml.twig', 'cache' => 'toc'])
            ->setHeader(['template' => 'pdf/header.html.twig', 'cache' => 'header'])
            ->setFooter(['template' => 'pdf/footer.html.twig', 'cache' => 'footer'])
            ->setContent(['controller' => ContentController::class, 'cache' => 'content'])
            ->setOptions([
                'margin-top' => 16,
                'margin-bottom' => 16,
                'header-spacing' => 5,
                'footer-spacing' => 5,
            ])
        ;
    
        return new InlinePdfResponse($this->pdfBuilder->getPdf());
    }
}
```

In projects other than Symfony you need to instantiate the Pdf object yourself
and inject the Snappy instance. Optionally you can inject templating, the HTTP
kernel and the request stack to enable all types of assets. The data and file
asset have no dependencies and will always be available.

## Versioning

Version numbers follow the MAJOR.MINOR.PATCH scheme. Backwards compatible
changes will be kept to a minimum but be aware that these can occur. Lock
your dependencies for production and test your code when upgrading.

## License

This bundle is under the MIT license. For the full copyright and license
information please view the LICENSE file that was distributed with this source code.
