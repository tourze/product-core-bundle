<?php

namespace Tourze\ProductCoreBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\ProductCoreBundle\Param\ProductSkuDetailParam;
use Tourze\ProductCoreBundle\Repository\SkuRepository;

#[MethodTag(name: '产品模块')]
#[MethodDoc(summary: '获取sku详情')]
#[MethodExpose(method: 'ProductSkuDetail')]
final class ProductSkuDetail extends BaseProcedure
{
    public function __construct(
        private readonly SkuRepository $skuRepository,
    ) {
    }

    /**
     * @phpstan-param ProductSkuDetailParam $param
     */
    public function execute(ProductSkuDetailParam|RpcParamInterface $param): ArrayResult
    {
        $sku = $this->skuRepository->find((int) $param->skuId);
        if (null === $sku) {
            throw new ApiException('找不到SKU');
        }
        if (($sku->getSpu()?->isValid() ?? false) === false) {
            throw new ApiException('SPU已下架');
        }
        if (($sku->isValid() ?? false) === false) {
            throw new ApiException('SKU已下架');
        }

        return $sku->retrieveSkuArray();
    }
}
