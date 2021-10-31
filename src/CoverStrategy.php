<?php

declare(strict_types=1);

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
    public const AUTO = 'auto';
    public const MERGE = 'merge';
    public const PARAM = 'param';

    private string $name;

    private function __construct(string $name)
    {
        if (!in_array($name, $this->getAvailableOptions())) {
            throw new Exception(sprintf('Invalid option "%s"', $name));
        }

        $this->name = $name;
    }

    public static function create(string $name): self
    {
        return new self($name);
    }

    public function equals(string $name): bool
    {
        return $this->name === $name;
    }

    /** @return array<string> */
    public function getAvailableOptions(): array
    {
        $reflectionClass = new ReflectionClass(__CLASS__);
        $constants = $reflectionClass->getConstants();

        foreach ($constants as $key => $constant) {
            $constants[$key] = strval($constant);
        }

        return $constants;
    }
}
