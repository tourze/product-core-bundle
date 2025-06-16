<?php

namespace ProductBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum PriceConditionOperator: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case GTE = 'gte';
    case GT = 'gt';
    case LTE = 'lte';
    case LT = 'lt';
    case EQUAL = 'equal';

    public function getLabel(): string
    {
        return match ($this) {
            self::GTE => '大于等于',
            self::GT => '大于',
            self::LTE => '小于等于',
            self::LT => '小于',
            self::EQUAL => '等于',
        };
    }
}
