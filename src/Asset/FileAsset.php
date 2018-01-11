<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Asset;

final class FileAsset extends AbstractAsset
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getData(): string
    {
        return file_get_contents($this->path);
    }
}
