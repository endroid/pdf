<?php

declare(strict_types=1);

namespace Endroid\Pdf;

interface PdfInterface
{
    public string $cover { set; }

    public CoverStrategy $coverStrategy { set; }

    public string $content { set; }

    /** @var array<mixed> */
    public array $options { set; }

    public function generate(): string;
}
