<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class ProductSpuDetailParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: 'SPU ID')]
        public string $spuId,
    ) {
    }
}
