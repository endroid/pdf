<?php

declare(strict_types=1);

namespace Endroid\Pdf\Builder;

use Endroid\Asset\AssetInterface;
use Endroid\Asset\Factory\AssetFactory;
use Endroid\Pdf\CoverStrategy;
use Endroid\Pdf\Pdf;

final class PdfBuilder
{
    public function __construct(
        private Pdf $pdf,
        private AssetFactory $assetFactory
    ) {
    }

    /** @param array<mixed> $options */
    public function setCover(array $options): self
    {
        if (isset($options['strategy'])) {
            $this->pdf->setCoverStrategy(CoverStrategy::create($options['strategy']));
            unset($options['strategy']);
        }

        $this->pdf->setCover(strval($this->createAsset($options)));

        return $this;
    }

    /** @param array<mixed> $options */
    public function setTableOfContents(array $options): self
    {
        $this->pdf->setTableOfContents(strval($this->createAsset($options)));

        return $this;
    }

    /** @param array<mixed> $options */
    public function setHeader(array $options): self
    {
        $this->pdf->setHeader(strval($this->createAsset($options)));

        return $this;
    }

    /** @param array<mixed> $options */
    public function setFooter(array $options): self
    {
        $this->pdf->setFooter(strval($this->createAsset($options)));

        return $this;
    }

    /** @param array<mixed> $options */
    public function setContent(array $options): self
    {
        $this->pdf->setContent(strval($this->createAsset($options)));

        return $this;
    }

    /** @param array<mixed> $options */
    public function setOptions(array $options): self
    {
        $this->pdf->setOptions($options);

        return $this;
    }

    public function getPdf(): Pdf
    {
        return $this->pdf;
    }

    /** @param array<mixed> $options */
    private function createAsset(array $options): AssetInterface
    {
        return $this->assetFactory->create(null, $options);
    }
}
