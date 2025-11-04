<?php

namespace Tourze\ProductCoreBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\ProductCoreBundle\Entity\Attribute;
use Tourze\ProductCoreBundle\Entity\AttributeGroup;
use Tourze\ProductCoreBundle\Entity\AttributeValue;
use Tourze\ProductCoreBundle\Entity\Brand;
use Tourze\ProductCoreBundle\Entity\CategoryAttribute;
use Tourze\ProductCoreBundle\Entity\Price;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\SkuAttribute;
use Tourze\ProductCoreBundle\Entity\SkuPackage;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Entity\SpuAttribute;

#[MenuProvider]
final readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        $root = $item->getChild('电商中心');
        if (null === $root) {
            $root = $item->addChild('电商中心');
        }
        $root->addChild('产品管理/SPU')->setUri($this->linkGenerator->getCurdListPage(Spu::class));
        $root->addChild('商品规格/SKU')->setUri($this->linkGenerator->getCurdListPage(Sku::class));
        $root->addChild('分类管理')->setUri($this->linkGenerator->getCurdListPage(Catalog::class));
        $root->addChild('品牌管理')->setUri($this->linkGenerator->getCurdListPage(Brand::class));
        $root->addChild('价格管理')->setUri($this->linkGenerator->getCurdListPage(Price::class));
        $root->addChild('SKU打包')->setUri($this->linkGenerator->getCurdListPage(SkuPackage::class));

        // 属性管理菜单
        $root->addChild('商品属性')
            ->setLabel('商品属性')
            ->setUri($this->linkGenerator->getCurdListPage(Attribute::class))
            ->setAttribute('icon', 'fas fa-tag')
            ->setExtra('help', '管理商品的基础属性，包括销售属性、非销售属性和自定义属性')
        ;

        // 属性值管理菜单
        $root->addChild('属性值')
            ->setLabel('属性值')
            ->setUri($this->linkGenerator->getCurdListPage(AttributeValue::class))
            ->setAttribute('icon', 'fas fa-list-ul')
            ->setExtra('help', '管理属性的可选值，支持文本、颜色和图片等形式')
        ;

        // 属性分组管理菜单
        $root->addChild('属性分组')
            ->setLabel('属性分组')
            ->setUri($this->linkGenerator->getCurdListPage(AttributeGroup::class))
            ->setAttribute('icon', 'fas fa-layer-group')
            ->setExtra('help', '管理属性的分组，用于在前端展示时对属性进行归类')
        ;

        // 类目属性关联管理菜单
        $root->addChild('类目属性关联')
            ->setLabel('类目属性关联')
            ->setUri($this->linkGenerator->getCurdListPage(CategoryAttribute::class))
            ->setAttribute('icon', 'fas fa-link')
            ->setExtra('help', '管理商品类目与属性的关联关系')
        ;

        // SPU属性管理菜单
        $root->addChild('SPU属性')
            ->setLabel('SPU属性')
            ->setUri($this->linkGenerator->getCurdListPage(SpuAttribute::class))
            ->setAttribute('icon', 'fas fa-cube')
            ->setExtra('help', '管理SPU（标准产品单元）的属性值')
        ;

        // SKU属性管理菜单
        $root->addChild('SKU属性')
            ->setLabel('SKU属性')
            ->setUri($this->linkGenerator->getCurdListPage(SkuAttribute::class))
            ->setAttribute('icon', 'fas fa-cubes')
            ->setExtra('help', '管理SKU（库存保管单元）的销售属性值')
        ;
    }
}
