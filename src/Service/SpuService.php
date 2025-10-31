<?php

namespace Tourze\ProductCoreBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Repository\SpuRepository;

/**
 * SPU 服务
 */
#[Autoconfigure(public: true)]
final class SpuService
{
    public function __construct(
        private readonly SpuRepository $spuRepository,
    ) {
    }

    /**
     * 根据ID查找SPU（不限制valid状态）
     */
    public function findSpuById(int|string $spuId): ?Spu
    {
        return $this->spuRepository->findOneBy([
            'id' => $spuId,
        ]);
    }

    /**
     * 根据ID查找有效的SPU
     */
    public function findValidSpuById(int|string $spuId): ?Spu
    {
        return $this->spuRepository->findOneBy([
            'id' => $spuId,
            'valid' => true,
        ]);
    }

    /**
     * 根据ID或GTIN查找有效的SPU
     */
    public function findValidSpuByIdOrGtin(int|string $value): ?Spu
    {
        $spu = $this->spuRepository->findOneBy([
            'id' => $value,
            'valid' => true,
        ]);

        if (null === $spu) {
            $spu = $this->spuRepository->findOneBy([
                'gtin' => $value,
                'valid' => true,
            ]);
        }

        return $spu;
    }

    /**
     * 查找所有有效的SPU
     *
     * @return iterable<Spu>
     */
    public function findAllValidSpus(): iterable
    {
        foreach ($this->spuRepository->findBy(['valid' => true]) as $item) {
            yield $item;
        }
    }

    /**
     * 根据分类ID查找SPU ID列表
     *
     * @return array<string>
     */
    public function findSpuIdsByCatalogId(string $catalogId): array
    {
        $result = $this->spuRepository->createQueryBuilder('s')
            ->select('s.id')
            ->join('s.categories', 'c')
            ->where('c.id = :catalogId')
            ->setParameter('catalogId', $catalogId)
            ->getQuery()
            ->getResult()
        ;

        return is_array($result) ? array_column($result, 'id') : [];
    }

    /**
     * 生成按分类查找SPU的DQL
     */
    public function getSpuIdsByCatalogDQL(): string
    {
        return $this->spuRepository->createQueryBuilder('s')
            ->select('s.id')
            ->join('s.categories', 'c')
            ->where('c.id = :categoryId')
            ->getDQL()
        ;
    }

    /**
     * 生成按分类查找SPU的DQL（别名方法）
     */
    public function getSpuIdsByCategoryDQL(): string
    {
        return $this->getSpuIdsByCatalogDQL();
    }
}
