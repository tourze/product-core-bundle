<?php

namespace Tourze\ProductCoreBundle;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Tourze\EnumExtra\SelectDataFetcher;

#[Autoconfigure(public: true)]
#[AutoconfigureTag(name: 'product.type.provider')]
final class ProductTypeProvider implements SelectDataFetcher
{
    public function genSelectData(): iterable
    {
        $title = $_ENV['NORMAL_TYPE_SPU_NAME'] ?? '通用商品';
        if (!is_string($title)) {
            $title = is_scalar($title) ? (string) $title : '通用商品';
        }
        yield [
            'label' => $title,
            'text' => $title,
            'value' => 'normal',
            'name' => $title,
        ];
    }
}
