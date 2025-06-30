<?php

namespace Tourze\ProductCoreBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 运费计费方式
 */
enum FreightValuationType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case FIXED = 'fixed';
    case BY_ITEM = 'by_item';
    //    case BY_WEIGHT = 'by_weight';

    public function getLabel(): string
    {
        return match ($this) {
            self::FIXED => '固定运费',
            self::BY_ITEM => '按件',
            //            self::BY_WEIGHT => '按重量',
        };
    }
}
