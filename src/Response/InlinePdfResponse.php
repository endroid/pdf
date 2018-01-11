<?php

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
    public function __construct(string $content = '')
    {
        parent::__construct($content);

        $this->headers->add(['Content-Type' => 'application/pdf']);
        $this->headers->add(['Content-Disposition' => $this->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, 'output.pdf')]);
    }
}
