<?php

declare(strict_types=1);

namespace Endroid\Pdf\Tests;

use Endroid\Asset\Factory\Adapter\DataAssetFactoryAdapter;
use Endroid\Asset\Factory\AssetFactory;
use Endroid\Pdf\Builder\PdfBuilder;
use Endroid\Pdf\Pdf;
use PHPUnit\Framework\TestCase;

final class PdfTest extends TestCase
{
    public function testGeneratePdf(): void
    {
        $pdf = new Pdf();

        $assetFactory = new AssetFactory();
        $assetFactory->addFactory(new DataAssetFactoryAdapter());

        $pdf = (new PdfBuilder($pdf, $assetFactory))
            ->setContent(['data' => '<html><head><title>PDF</title></head><body>PDF Content</body></html>'])
            ->getPdf()
        ;

        $this->assertStringStartsWith('%PDF', $pdf->generate());
    }
}
