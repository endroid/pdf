<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf;

use Endroid\Pdf\Asset\AbstractAsset;
use Endroid\Pdf\Asset\ControllerAsset;
use Endroid\Pdf\Asset\DataAsset;
use Endroid\Pdf\Asset\FileAsset;
use Endroid\Pdf\Asset\TemplateAsset;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use Knp\Snappy\Pdf as Snappy;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Environment;

final class Pdf
{
    private $snappy;
    private $kernel;
    private $requestStack;
    private $templating;
    private $content;

    public function __construct(
        Snappy $snappy,
        HttpKernelInterface $kernel = null,
        RequestStack $requestStack = null,
        Environment $templating = null
    ) {
        $this->snappy = $snappy;
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        $this->templating = $templating;
    }

    public function setCover(string $assetSource, array $assetParameters = []): void
    {
        $this->snappy->setOption('cover', $this->createAsset($assetSource, $assetParameters));
    }

    public function setTableOfContents(string $assetSource, array $assetParameters = []): void
    {
        $this->snappy->setOption('toc', true);
        $this->snappy->setOption('xsl-style-sheet', $this->createAsset($assetSource, $assetParameters));
    }

    public function setHeader(string $assetSource, array $assetParameters = []): void
    {
        $this->snappy->setOption('header-html', $this->createAsset($assetSource, $assetParameters));
    }

    public function setFooter(string $assetSource, array $assetParameters = []): void
    {
        $this->snappy->setOption('footer-html', $this->createAsset($assetSource, $assetParameters));
    }

    public function setContent(string $assetSource, array $assetParameters = []): void
    {
        $this->content = $this->createAsset($assetSource, $assetParameters);
    }

    public function createAsset(string $assetSource, array $assetParameters = []): AbstractAsset
    {
        if (class_exists($assetSource)) {
            return new ControllerAsset($this->kernel, $this->requestStack, $assetSource, $assetParameters);
        }

        if (file_exists($assetSource)) {
            return new FileAsset($assetSource);
        }

        if ($this->templating->getLoader()->exists($assetSource)) {
            return new TemplateAsset($this->templating, $assetSource, $assetParameters);
        }

        return new DataAsset($assetSource);
    }

    public function setOptions(array $options = []): void
    {
        $this->snappy->setOptions($options);
    }

    public function generate(): string
    {
        $coverPdf = $this->createCoverPdf();
        if ($coverPdf instanceof Pdf) {
            $this->snappy->setOption('cover', null);
        }

        $pdf = $this->snappy->getOutputFromHtml($this->content);

        if ($coverPdf instanceof Pdf) {
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

        if (!$options['cover'] instanceof AbstractAsset) {
            return null;
        }

        if (!$this->hasMargins()) {
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

    private function hasMargins()
    {
        $options = $this->snappy->getOptions();

        if ($options['header-html'] || $options['footer-html']) {
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
