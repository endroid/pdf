<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Builder;

use Endroid\Asset\AbstractAsset;
use Endroid\Asset\Factory\AssetFactory;
use Endroid\Pdf\CoverStrategy;
use Endroid\Pdf\Pdf;

final class PdfBuilder
{
    private $pdf;
    private $assetFactory;

    public function __construct(Pdf $pdf, AssetFactory $assetFactory)
    {
        $this->pdf = $pdf;
        $this->assetFactory = $assetFactory;
    }

    public function setCover(array $options): self
    {
        if (isset($options['strategy'])) {
            $this->pdf->setCoverStrategy(CoverStrategy::create($options['strategy']));
            unset($options['strategy']);
        }

        $this->pdf->setCover($this->createAsset($options));

        return $this;
    }

    public function setTableOfContents(array $options): self
    {
        $this->pdf->setTableOfContents($this->createAsset($options));

        return $this;
    }

    public function setHeader(array $options): self
    {
        $this->pdf->setHeader($this->createAsset($options));

        return $this;
    }

    public function setFooter(array $options): self
    {
        $this->pdf->setFooter($this->createAsset($options));

        return $this;
    }

    public function setContent(array $options): self
    {
        $this->pdf->setContent($this->createAsset($options));

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->pdf->setOptions($options);

        return $this;
    }

    public function getPdf(): Pdf
    {
        return $this->pdf;
    }

    private function createAsset(array $options): AbstractAsset
    {
        return $this->assetFactory->create($options);
    }
}
