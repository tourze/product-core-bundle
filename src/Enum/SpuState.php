<?php

namespace Tourze\ProductCoreBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SpuState: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ONLINE = '1';
    case OFFLINE = '0';

    public function getLabel(): string
    {
        return match ($this) {
            self::ONLINE => '上架中',
            self::OFFLINE => '已上架',
        };
    }
}
