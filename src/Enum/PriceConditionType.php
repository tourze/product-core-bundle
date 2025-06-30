<?php

namespace Tourze\ProductCoreBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * @see https://club.kdcloud.com/article/264809793977174784?productLineId=2&isKnowledge=2 阶梯价格的取价策略配置介绍
 */
enum PriceConditionType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case BUY_QUANTITY = 'buy-quantity';

    public function getLabel(): string
    {
        return match ($this) {
            self::BUY_QUANTITY => '购买数量',
        };
    }
}
