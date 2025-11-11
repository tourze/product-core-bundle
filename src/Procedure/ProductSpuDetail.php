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
use Tourze\ProductCoreBundle\Exception\ParameterValidationException;
use Tourze\ProductCoreBundle\Repository\SpuRepository;
use Tourze\UserIDBundle\Model\SystemUser;

#[MethodTag(name: '产品模块')]
#[MethodDoc(summary: '获取SPU详情')]
#[MethodExpose(method: 'ProductSpuDetail')]
final class ProductSpuDetail extends CacheableProcedure
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
        $spu = $this->findSpu();
        $result = $spu->retrieveSpuArray();
        $result = $this->filterInvalidSkus($result);

        if (null === $this->security->getUser()) {
            return $result;
        }

        return $this->dispatchSpuDetailEvent($spu, $result);
    }

    private function findSpu(): Spu
    {
        $spu = $this->spuRepository->findOneBy([
            'id' => (int) $this->spuId,
            'valid' => true,
        ]);
        if (null === $spu) {
            throw new ApiException('找不到产品');
        }

        return $spu;
    }

    /**
     * @param array<string,mixed> $result
     * @return array<string,mixed>
     */
    private function filterInvalidSkus(array $result): array
    {
        if (isset($result['skus']) && is_array($result['skus']) && [] !== $result['skus']) {
            foreach ($result['skus'] as $k => $sku) {
                if (is_array($sku) && false === ($sku['valid'] ?? true)) {
                    unset($result['skus'][$k]);
                }
            }
            $result['skus'] = array_values($result['skus']);
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $result
     * @return array<string,mixed>
     */
    private function dispatchSpuDetailEvent(Spu $spu, array $result): array
    {
        $event = new SpuDetailEvent();
        $event->setSpu($spu);
        $event->setResult($result);
        $user = $this->security->getUser();
        if (null !== $user) {
            $event->setSender($user);
        }
        $event->setReceiver(SystemUser::instance());
        $this->eventDispatcher->dispatch($event);

        $result = $event->getResult();
        /** @var array<string, mixed> $result */
        return $result;
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $params = $request->getParams();
        if (null === $params) {
            throw new ParameterValidationException('Request params cannot be null');
        }

        $key = self::buildParamCacheKey($params);
        if (null !== $this->security->getUser()) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60; // 1 minute
    }

    /**
     * @return iterable<string>
     */
    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Spu::class);
        yield CacheHelper::getClassTags(Sku::class);
    }
}
