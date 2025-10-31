<?php

namespace Tourze\ProductCoreBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 配送方式
 */
enum DeliveryType: string implements Labelable, Itemable, Selectable, BadgeInterface
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

    public function getBadge(): string
    {
        return match ($this) {
            self::EXPRESS => self::SUCCESS,
            self::STORE => self::INFO,
        };
    }
}
