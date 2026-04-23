<?php

declare(strict_types=1);

namespace Endroid\Pdf;

use Dompdf\Dompdf;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;

final class Pdf implements PdfInterface, \Stringable
{
    private string $coverHtml = '';

    /** @var array<mixed> */
    private array $dompdfOptions = [];

    public string $cover {
        set {
            $this->coverHtml = $value;
        }
    }

    public string $content;

    /** @var array<mixed> */
    public array $options {
        set {
            $this->dompdfOptions = $value;
        }
    }

    public function __construct(
        public CoverStrategy $coverStrategy = CoverStrategy::Auto,
    ) {}

    #[\Override]
    public function generate(): string
    {
        $coverPdf = $this->createCoverPdf();

        $dompdf = $this->createDompdf();
        $dompdf->loadHtml($this->content);
        $dompdf->render();
        $pdf = $dompdf->output();

        if (null !== $coverPdf) {
            $pdfMerger = new Merger();
            $pdfMerger->addRaw($coverPdf, new Pages('1'));
            $pdfMerger->addRaw($pdf);
            $pdf = $pdfMerger->merge();
        }

        return $pdf;
    }

    private function createCoverPdf(): ?string
    {
        if ('' === $this->coverHtml) {
            return null;
        }

        $dompdf = $this->createDompdf();
        $dompdf->loadHtml($this->coverHtml);
        $dompdf->render();

        return $dompdf->output();
    }

    private function createDompdf(): Dompdf
    {
        return new Dompdf(['pdfBackend' => 'auto', ...$this->dompdfOptions]);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->generate();
    }
}
