<?php

namespace Tourze\ProductCoreBundle;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Tourze\EnumExtra\SelectDataFetcher;

#[AutoconfigureTag(name: 'product.type.provider')]
class ProductTypeProvider implements SelectDataFetcher
{
    public function genSelectData(): iterable
    {
        $title = $_ENV['NORMAL_TYPE_SPU_NAME'] ?? '通用商品';
        yield [
            'label' => $title,
            'text' => $title,
            'value' => 'normal',
            'name' => $title,
        ];
    }
}
