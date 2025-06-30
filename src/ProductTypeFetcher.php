<?php

namespace Tourze\ProductCoreBundle;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Tourze\EnumExtra\SelectDataFetcher;

/**
 * 读取产品类型数据
 */
class ProductTypeFetcher implements SelectDataFetcher
{
    public function __construct(
        #[TaggedIterator(tag: 'product.type.provider')] private readonly iterable $providers,
    ) {
    }

    public function genSelectData(): iterable
    {
        $result = [];
        foreach ($this->providers as $provider) {
            /** @var SelectDataFetcher $provider */
            $subData = $provider->genSelectData();
            // Skip interrupt check as AntdCpBundle is not available

            /* @var SelectDataFetcher $provider */
            if (!is_array($subData)) {
                $subData = iterator_to_array($subData);
            }
            $result = array_merge($result, $subData);
        }

        return array_values($result);
    }
}
