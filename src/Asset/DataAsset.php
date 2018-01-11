<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Asset;

final class DataAsset extends AbstractAsset
{
    private $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function getData(): string
    {
        return $this->data;
    }
}
