<?php

namespace Tourze\ProductCoreBundle;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Tourze\EnumExtra\SelectDataFetcher;

/**
 * 读取标签数据
 */
#[Autoconfigure(public: true)]
final class ProductTagFetcher implements SelectDataFetcher
{
    /**
     * @param iterable<SelectDataFetcher> $providers
     */
    public function __construct(
        #[AutowireIterator(tag: 'product.tag.provider')] private readonly iterable $providers,
    ) {
    }

    public function genSelectData(): iterable
    {
        $result = [];
        foreach ($this->providers as $provider) {
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
