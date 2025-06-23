<?php

namespace ProductCoreBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 库存状态
 *
 * @see https://zhidao.baidu.com/question/452908105.html
 */
enum StockState: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case MAKING = 'making';
    case STOCKING = 'stocking';
    case SOLD = 'sold';
    case SHIPPING = 'shipping';
    case LOST = 'lost';
    case ABANDON = 'abandon';

    public function getLabel(): string
    {
        return match ($this) {
            self::MAKING => '在制', // 也可以叫做在购
            self::STOCKING => '在库',
            self::SOLD => '已售',
            self::SHIPPING => '在途',
            self::LOST => '丢失',
            self::ABANDON => '废弃',
        };
    }
}
