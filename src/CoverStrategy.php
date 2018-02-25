<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf;

use Exception;
use ReflectionClass;

final class CoverStrategy
{
    const AUTO = 'auto';
    const MERGE = 'merge';
    const PARAM = 'param';

    private $name;

    private function __construct(string $name)
    {
        if (!in_array($name, $this->getAvailableOptions())) {
            throw new Exception(sprintf('Invalid option "%s"', $name));
        }

        $this->name = $name;
    }

    public static function create(string $name)
    {
        return new self($name);
    }

    public function equals(string $name)
    {
        return $this->name === $name;
    }

    public function getAvailableOptions(): array
    {
        $reflectionClass = new ReflectionClass(__CLASS__);

        return $reflectionClass->getConstants();
    }
}
