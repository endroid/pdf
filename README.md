# PDF

*By [endroid](https://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![Build Status](http://img.shields.io/travis/endroid/pdf.svg)](http://travis-ci.org/endroid/pdf)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)
[![License](http://img.shields.io/packagist/l/endroid/pdf.svg)](https://packagist.org/packages/endroid/pdf)

Library for quickly generating PDF files.

## Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require endroid/pdf
```

## Usage (in Symfony)

When installed with Flex, a recipe is executed that registers the Pdf class as
a service and automatically injects available services. For instance when
templating is available you can use Twig based assets and when an HTTP kernel
and request stack are available controller actions can be used as asset.

```php
<?php

namespace App\Controller\Pdf;

use Endroid\Pdf\Pdf;
use Endroid\Pdf\Response\InlinePdfResponse;
use Symfony\Component\HttpFoundation\Response;

final class PdfController
{
    public function __invoke(Pdf $pdf): Response
    {
        $pdf->setCover(CoverController::class); // controller
        $pdf->setTableOfContents('pdf/table_of_contents.xml.twig'); // template
        $pdf->setHeader('pdf/header.html.twig');
        $pdf->setFooter('pdf/footer.html.twig');
        $pdf->setContent(ContentController::class);
        
        return new InlinePdfResponse($pdf);
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
