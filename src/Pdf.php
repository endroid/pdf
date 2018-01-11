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

    public function getSnappy(): Snappy
    {
        return $this->snappy;
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

    public function generate(): string
    {
        return $this->snappy->getOutputFromHtml($this->content);
    }

    public function __toString()
    {
        return $this->generate();
    }
}
