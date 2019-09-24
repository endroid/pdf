<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class InlinePdfResponse extends Response
{
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
