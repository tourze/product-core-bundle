<?php

namespace ProductBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * spu限制规则
 */
enum SpuLimitType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case BUY_TOTAL = 'buy-total';
    case BUY_YEAR = 'buy-year';
    case BUY_QUARTER = 'buy-quarter';
    case BUY_MONTH = 'buy-month';
    case BUY_DAILY = 'buy-daily';
    case SPECIFY_COUPON = 'specify-coupon';
    case SPU_MUTEX = 'spu-mutex';
    case BUY_MONTH_STORE = 'buy-month-store';
    case BUY_QUARTER_STORE = 'buy-quarter-store';
    case BUY_YEAR_STORE = 'buy-year-store';
    case BUY_STORE_TOTAL = 'buy-store-total';

    public function getLabel(): string
    {
        return match ($this) {
            self::BUY_TOTAL => '总次数限购',
            self::BUY_YEAR => '按年度限购',
            self::BUY_QUARTER => '按季度限购',
            self::BUY_MONTH => '按月度限购',
            self::BUY_DAILY => '按日限购',
            self::SPECIFY_COUPON => '特定优惠券购买',
            self::SPU_MUTEX => 'SPU购买互斥',
            self::BUY_MONTH_STORE => '按月度门店限购',
            self::BUY_QUARTER_STORE => '按季度门店限购',
            self::BUY_YEAR_STORE => '按年度门店限购',
            self::BUY_STORE_TOTAL => '按门店总次数限购',
        };
    }
}
