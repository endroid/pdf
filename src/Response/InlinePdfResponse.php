<?php

declare(strict_types=1);

namespace Endroid\Pdf\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class InlinePdfResponse extends Response
{
    /** @param array<string, string> $headers */
    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        parent::__construct($content, $status, $headers);

        $this->headers->add([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $this->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                'output.pdf'
            ),
        ]);
    }
}
