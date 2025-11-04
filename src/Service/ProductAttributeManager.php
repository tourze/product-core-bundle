<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\SkuAttribute;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Entity\SpuAttribute;
use Tourze\ProductCoreBundle\Repository\SkuAttributeRepository;
use Tourze\ProductCoreBundle\Repository\SpuAttributeRepository;

#[Autoconfigure(public: true)]
readonly class ProductAttributeManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SpuAttributeRepository $spuAttributeRepository,
        private SkuAttributeRepository $skuAttributeRepository,
    ) {
    }

    /**
     * 设置SPU属性值
     */
    public function assignSpuAttributeValue(
        Spu $spu,
        string $name,
        string $value,
    ): SpuAttribute {
        $spuAttribute = $this->spuAttributeRepository->findBySpuAndName($spu, $name);

        if (null === $spuAttribute) {
            $spuAttribute = new SpuAttribute();
            $spuAttribute->setSpu($spu);
            $spuAttribute->setName($name);
        }

        $spuAttribute->setValue($value);
        $this->entityManager->persist($spuAttribute);

        return $spuAttribute;
    }

    /**
     * 批量设置SPU属性
     *
     * @param Spu $spu
     * @param array<array{name: string, value: string}> $attributesData
     * @return array<SpuAttribute>
     */
    public function batchSetSpuAttributes(Spu $spu, array $attributesData): array
    {
        $result = [];
        foreach ($attributesData as $attributeData) {
            $result[] = $this->assignSpuAttributeValue(
                $spu,
                $attributeData['name'],
                $attributeData['value']
            );
        }

        $this->entityManager->flush();

        return $result;
    }

    /**
     * 获取SPU的所有属性
     *
     * @return array<SpuAttribute>
     */
    public function getSpuAttributes(Spu $spu): array
    {
        return $this->spuAttributeRepository->findBySpu($spu);
    }

    /**
     * 设置SKU属性值
     */
    public function assignSkuAttributeValue(
        Sku $sku,
        string $name,
        string $value,
    ): SkuAttribute {
        $skuAttribute = $this->skuAttributeRepository->findBySkuAndName($sku, $name);

        if (null === $skuAttribute) {
            $skuAttribute = new SkuAttribute();
            $skuAttribute->setSku($sku);
            $skuAttribute->setName($name);
        }

        $skuAttribute->setValue($value);
        $this->entityManager->persist($skuAttribute);

        return $skuAttribute;
    }

    /**
     * 批量设置SKU属性
     *
     * @param Sku $sku
     * @param array<array{name: string, value: string}> $attributesData
     * @return array<SkuAttribute>
     */
    public function batchSetSkuAttributes(Sku $sku, array $attributesData): array
    {
        $result = [];
        foreach ($attributesData as $attributeData) {
            $result[] = $this->assignSkuAttributeValue(
                $sku,
                $attributeData['name'],
                $attributeData['value']
            );
        }

        $this->entityManager->flush();

        return $result;
    }

    /**
     * 获取SKU的所有属性
     *
     * @return array<SkuAttribute>
     */
    public function getSkuAttributes(Sku $sku): array
    {
        return $this->skuAttributeRepository->findBySku($sku);
    }

    /**
     * 删除SPU属性
     */
    public function removeSpuAttribute(SpuAttribute $spuAttribute): void
    {
        $this->entityManager->remove($spuAttribute);
        $this->entityManager->flush();
    }

    /**
     * 删除SKU属性
     */
    public function removeSkuAttribute(SkuAttribute $skuAttribute): void
    {
        $this->entityManager->remove($skuAttribute);
        $this->entityManager->flush();
    }
}
