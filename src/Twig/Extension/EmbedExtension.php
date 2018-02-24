<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EmbedExtension extends AbstractExtension
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('embed', array($this, 'embed')),
        ];
    }

    public function embed(string $source): string
    {
        $data = file_get_contents($source);

        return 'data:'.$this->getMimeType($data).';base64,'.base64_encode($data);
    }

    private function getMimeType(string $data): string
    {
        $fileInfo = finfo_open();
        $mimeType = finfo_buffer($fileInfo, $data, FILEINFO_MIME_TYPE);
        finfo_close($fileInfo);

        return $mimeType;
    }
}
