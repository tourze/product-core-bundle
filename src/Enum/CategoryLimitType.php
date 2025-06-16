<?php

namespace ProductBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * Category限制规则
 */
enum CategoryLimitType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case BUY_TOTAL = 'buy-total';
    case BUY_YEAR = 'buy-year';
    case BUY_QUARTER = 'buy-quarter';
    case BUY_MONTH = 'buy-month';
    case BUY_DAILY = 'buy-daily';
    case SPECIFY_COUPON = 'specify-coupon';

    public function getLabel(): string
    {
        return match ($this) {
            self::BUY_TOTAL => '总次数限购',
            self::BUY_YEAR => '按年度限购',
            self::BUY_QUARTER => '按季度限购',
            self::BUY_MONTH => '按月度限购',
            self::BUY_DAILY => '按日限购',
            self::SPECIFY_COUPON => '特定优惠券购买',
        };
    }
}
