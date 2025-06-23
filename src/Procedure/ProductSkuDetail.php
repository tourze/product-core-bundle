<?php

namespace ProductCoreBundle\Procedure;

use ProductCoreBundle\Repository\SkuRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag('产品模块')]
#[MethodDoc('获取sku详情')]
#[MethodExpose('ProductSkuDetail')]
class ProductSkuDetail extends BaseProcedure
{
    #[MethodParam('SKU ID')]
    public string $skuId;

    public function __construct(
        private readonly SkuRepository $skuRepository,
    ) {
    }

    public function execute(): array
    {
        $sku = $this->skuRepository->find($this->skuId);
        if ($sku === null) {
            throw new ApiException('找不到SKU');
        }
        if (!$sku->getSpu()?->isValid()) {
            throw new ApiException('SPU已下架');
        }
        if (!$sku->isValid()) {
            throw new ApiException('SKU已下架');
        }

        return $sku->retrieveSkuArray();
    }
}
