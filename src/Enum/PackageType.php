<?php

namespace ProductBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 打包物品类型
 */
enum PackageType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case COUPON = 'coupon';

    public function getLabel(): string
    {
        return match ($this) {
            self::COUPON => '优惠券',
        };
    }
}
