<?php

declare(strict_types=1);

namespace Endroid\Pdf;

use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use Knp\Snappy\Pdf as Snappy;

final class Pdf implements PdfInterface
{
    /** @var Snappy */
    private $snappy;

    /** @var CoverStrategy */
    private $coverStrategy;

    /** @var string */
    private $content;

    public function __construct(Snappy $snappy)
    {
        $this->snappy = $snappy;
        $this->coverStrategy = CoverStrategy::create(CoverStrategy::AUTO);
    }

    public function setCover(string $data): void
    {
        $this->snappy->setOption('cover', $data);
    }

    public function setCoverStrategy(CoverStrategy $coverStrategy): void
    {
        $this->coverStrategy = $coverStrategy;
    }

    public function setTableOfContents(string $data): void
    {
        $this->snappy->setOption('toc', true);
        $this->snappy->setOption('xsl-style-sheet', $data);
    }

    public function setHeader(string $data): void
    {
        $this->snappy->setOption('header-html', $data);
    }

    public function setFooter(string $data): void
    {
        $this->snappy->setOption('footer-html', $data);
    }

    public function setContent(string $data): void
    {
        $this->content = $data;
    }

    /** @param array<mixed> $options */
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

        $pdf = $this->snappy->getOutputFromHtml($this->content);

        if ($coverPdf instanceof self) {
            $pdfMerger = new Merger();
            $pdfMerger->addRaw((string) $coverPdf, new Pages('1'));
            $pdfMerger->addRaw((string) $pdf);
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
        $coverPdf->setContent($options['cover']);
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
