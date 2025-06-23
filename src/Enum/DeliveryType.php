<?php

namespace ProductCoreBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 配送方式
 */
enum DeliveryType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case EXPRESS = 'express';
    case STORE = 'store';

    public function getLabel(): string
    {
        return match ($this) {
            self::EXPRESS => '快递配送',
            self::STORE => '门店自提',
        };
    }
}
