<?php

namespace ProductBundle;

use AntdCpBundle\Builder\Field\SelectDataInterrupt;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Tourze\EnumExtra\SelectDataFetcher;

/**
 * 读取标签数据
 */
class ProductTagFetcher implements SelectDataFetcher
{
    public function __construct(
        #[TaggedIterator('product.tag.provider')] private readonly iterable $providers,
    ) {
    }

    public function genSelectData(): array
    {
        $result = [];
        foreach ($this->providers as $provider) {
            $subData = $provider->genSelectData();
            if ($provider instanceof SelectDataInterrupt && $provider->isInterrupt()) {
                return iterator_to_array($subData);
            }

            /* @var SelectDataFetcher $provider */
            if (!is_array($subData)) {
                $subData = iterator_to_array($subData);
            }
            $result = array_merge($result, $subData);
        }

        return array_values($result);
    }
}
