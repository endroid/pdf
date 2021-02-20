<?php

declare(strict_types=1);

namespace Endroid\Pdf;

interface PdfInterface
{
    public function setCover(string $data): void;

    public function setCoverStrategy(CoverStrategy $coverStrategy): void;

    public function setTableOfContents(string $data): void;

    public function setHeader(string $data): void;

    public function setFooter(string $data): void;

    public function setContent(string $data): void;

    /** @param array<mixed> $options */
    public function setOptions(array $options = []): void;

    public function generate(): string;
}
