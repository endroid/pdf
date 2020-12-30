<?php

declare(strict_types=1);

namespace Endroid\Pdf\Builder;

use Endroid\Asset\AssetInterface;
use Endroid\Asset\Factory\AssetFactory;
use Endroid\Pdf\CoverStrategy;
use Endroid\Pdf\Pdf;
use Endroid\Pdf\PdfInterface;

interface PdfBuilderInterface
{
    /** @param array<mixed> $options */
    public function setCover(array $options): self;

    /** @param array<mixed> $options */
    public function setTableOfContents(array $options): self;

    /** @param array<mixed> $options */
    public function setHeader(array $options): self;

    /** @param array<mixed> $options */
    public function setFooter(array $options): self;

    /** @param array<mixed> $options */
    public function setContent(array $options): self;

    /** @param array<mixed> $options */
    public function setOptions(array $options): self;

    public function getPdf(): PdfInterface;
}
