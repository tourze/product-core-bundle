<?php

namespace Tourze\ProductCoreBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum PriceType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case SALE = 'sale';
    case COST = 'cost';
    case COMPETE = 'compete';
    case FREIGHT = 'freight';
    case MARKETING = 'marketing';
    case ORIGINAL_PRICE = 'original_price';

    public function getLabel(): string
    {
        return match ($this) {
            self::SALE => '一口价',
            self::COST => '成本价格',
            self::COMPETE => '竞品价格',
            self::FREIGHT => '物流费用',
            self::MARKETING => '营销费用',
            self::ORIGINAL_PRICE => '原价',
        };
    }
}
