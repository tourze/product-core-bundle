<?php

namespace Tourze\ProductCoreBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineHelper\CacheHelper;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Event\SpuDetailEvent;
use Tourze\ProductCoreBundle\Repository\SpuRepository;
use Tourze\UserIDBundle\Model\SystemUser;

#[MethodTag(name: '产品模块')]
#[MethodDoc(summary: '获取SPU详情')]
#[MethodExpose(method: 'ProductSpuDetail')]
class ProductSpuDetail extends CacheableProcedure
{
    #[MethodParam(description: 'SPU ID')]
    public string $spuId;

    public function __construct(
        private readonly SpuRepository $spuRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $spu = $this->spuRepository->findOneBy([
            'id' => $this->spuId,
            'valid' => true,
        ]);
        if ($spu === null) {
            throw new ApiException('找不到产品');
        }
        $result = $spu->retrieveSpuArray();
        if (!empty($result['skus'])) {
            foreach ($result['skus'] as $k => $sku) {
                if (empty($sku['valid'])) {
                    unset($result['skus'][$k]);
                }
            }
            $result['skus'] = array_values($result['skus']);
        }

        if ($this->security->getUser() === null) {
            return $result;
        }

        $event = new SpuDetailEvent();
        $event->setSpu($spu);
        $event->setResult($result);
        $event->setSender($this->security->getUser());
        $event->setReceiver(SystemUser::instance());
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $key = static::buildParamCacheKey($request->getParams());
        if ($this->security->getUser() !== null) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60; // 1 minute
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Spu::class);
        yield CacheHelper::getClassTags(Sku::class);
    }
}
