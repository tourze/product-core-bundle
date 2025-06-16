<?php

namespace ProductBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum PriceTarget: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ARCHIVED = 'archived';
    case SALE = 'sale';

    public function getLabel(): string
    {
        return match ($this) {
            self::ARCHIVED => '存档',
            self::SALE => '销售',
        };
    }
}
