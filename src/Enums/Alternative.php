<?php

namespace HiFolks\Statistics\Enums;

enum Alternative: string
{
    case TwoSided = 'two-sided';
    case Greater = 'greater';
    case Less = 'less';
}
