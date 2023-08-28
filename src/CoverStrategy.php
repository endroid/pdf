<?php

declare(strict_types=1);

namespace Endroid\Pdf;

enum CoverStrategy: string
{
    case Auto = 'auto';
    case Merge = 'merge';
    case Param = 'param';
}
