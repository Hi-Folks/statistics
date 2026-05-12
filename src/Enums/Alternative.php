<?php

declare(strict_types=1);

namespace HiFolks\Statistics\Enums;

enum Alternative: string
{
    case TwoSided = 'two-sided';
    case Greater = 'greater';
    case Less = 'less';
}
