<?php

namespace ProductCoreBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 库存变动类型
 *
 * @see https://www.woshipm.com/pd/615772.html
 */
enum StockChange: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PUT = 'put';
    case LOCK = 'lock';
    case UNLOCK = 'unlock';
    case DEDUCT = 'deduct';
    case RETURN = 'return';

    public function getLabel(): string
    {
        return match ($this) {
            self::PUT => '入库',
            self::LOCK => '锁定',
            self::UNLOCK => '解锁',
            self::DEDUCT => '扣减',
            self::RETURN => '返还',
        };
    }
}
