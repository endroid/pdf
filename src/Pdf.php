<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf;

use Endroid\Pdf\Asset\AbstractAsset;
use Endroid\Pdf\Asset\DataAsset;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use Knp\Snappy\Pdf as Snappy;

final class Pdf
{
    private $snappy;
    private $content;
    private $coverStrategy;

    public function __construct(Snappy $snappy)
    {
        $this->snappy = $snappy;
        $this->coverStrategy = CoverStrategy::create(CoverStrategy::AUTO);
    }

    public function setCover(AbstractAsset $asset): void
    {
        $this->snappy->setOption('cover', $asset->getData());
    }

    public function setCoverStrategy(CoverStrategy $coverStrategy): void
    {
        $this->coverStrategy = $coverStrategy;
    }

    public function setTableOfContents(AbstractAsset $asset): void
    {
        $this->snappy->setOption('toc', true);
        $this->snappy->setOption('xsl-style-sheet', $asset->getData());
    }

    public function setHeader(AbstractAsset $asset): void
    {
        $this->snappy->setOption('header-html', $asset->getData());
    }

    public function setFooter(AbstractAsset $asset): void
    {
        $this->snappy->setOption('footer-html', $asset->getData());
    }

    public function setContent(AbstractAsset $asset): void
    {
        $this->content = $asset;
    }

    public function setOptions(array $options = []): void
    {
        $this->snappy->setOptions($options);
    }

    public function generate(): string
    {
        $coverPdf = $this->createCoverPdf();
        if ($coverPdf instanceof self) {
            $this->snappy->setOption('cover', null);
        }

        $pdf = $this->snappy->getOutputFromHtml($this->content->getData());

        if ($coverPdf instanceof self) {
            $pdfMerger = new Merger();
            $pdfMerger->addRaw($coverPdf, new Pages('1'));
            $pdfMerger->addRaw($pdf);
            $pdf = $pdfMerger->merge();
        }

        return $pdf;
    }

    private function createCoverPdf(): ?Pdf
    {
        $options = $this->snappy->getOptions();

        if (!$options['cover'] || $this->coverStrategy->equals(CoverStrategy::PARAM)) {
            return null;
        }

        if ($this->coverStrategy->equals(CoverStrategy::AUTO) && !$this->hasMargins()) {
            return null;
        }

        $coverPdf = clone $this;
        $coverPdf->setContent(new DataAsset($options['cover']));
        $coverPdf->setOptions([
            'cover' => null,
            'toc' => null,
            'xsl-style-sheet' => null,
            'header-html' => null,
            'footer-html' => null,
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
        ]);

        return $coverPdf;
    }

    private function hasMargins(): bool
    {
        $options = $this->snappy->getOptions();

        if (null !== $options['header-html'] || null !== $options['footer-html']) {
            return true;
        }

        if ($options['margin-top'] > 0 || $options['margin-bottom'] > 0) {
            return true;
        }

        if ($options['margin-left'] > 0 || $options['margin-right'] > 0) {
            return true;
        }

        return false;
    }

    public function __clone()
    {
        $this->snappy = clone $this->snappy;
    }

    public function __toString(): string
    {
        return $this->generate();
    }
}
