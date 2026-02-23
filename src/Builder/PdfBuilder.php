<?php

declare(strict_types=1);

namespace Endroid\Pdf\Builder;

use Endroid\Asset\AssetInterface;
use Endroid\Asset\Factory\AssetFactory;
use Endroid\Pdf\CoverStrategy;
use Endroid\Pdf\Pdf;

final readonly class PdfBuilder
{
    public function __construct(
        private Pdf $pdf,
        private AssetFactory $assetFactory,
    ) {}

    /** @param array<mixed> $options */
    public function setCover(array $options): self
    {
        if (array_key_exists('strategy', $options)) {
            $this->pdf->coverStrategy = CoverStrategy::from((string) $options['strategy']);
            unset($options['strategy']);
        }

        $this->pdf->cover = strval($this->createAsset($options));

        return $this;
    }

    /** @param array<mixed> $options */
    public function setContent(array $options): self
    {
        $this->pdf->content = strval($this->createAsset($options));

        return $this;
    }

    /** @param array<mixed> $options */
    public function setOptions(array $options): self
    {
        $this->pdf->options = $options;

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
