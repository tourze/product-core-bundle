<?php

namespace ProductBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * TODO 参考 https://www.zditect.com/main/magento-2/magento-2-product-types.html 补齐定义
 */
enum SpuType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NORMAL = 'normal';
    case PACKAGE = 'package';
    //    case VIRTUAL = 'virtual';

    public function getLabel(): string
    {
        return match ($this) {
            self::NORMAL => '普通商品',
            self::PACKAGE => '打包商品',
            //            self::VIRTUAL => '虚拟商品',
        };
    }
}
