<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Tests;

use Endroid\Asset\Factory\Adapter\DataAssetFactoryAdapter;
use Endroid\Asset\Factory\AssetFactory;
use Endroid\Pdf\Builder\PdfBuilder;
use Endroid\Pdf\Pdf;
use Knp\Snappy\Pdf as Snappy;
use PHPUnit\Framework\TestCase;

class PdfTest extends TestCase
{
    public function testNoTestsYet()
    {
        $snappy = new Snappy(__DIR__.'/../vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        $snappyPdf = new Pdf($snappy);

        $assetFactory = new AssetFactory();
        $assetFactory->addFactory(new DataAssetFactoryAdapter());

        $pdf = (new PdfBuilder($snappyPdf, $assetFactory))
            ->setContent(['data' => '<html><head><title>PDF</title></head><body>PDF Content</body></html>'])
            ->setOptions(['margin-top' => 10])
            ->getPdf()
        ;

        $this->assertStringStartsWith('%PDF', $pdf->generate());
    }
}
