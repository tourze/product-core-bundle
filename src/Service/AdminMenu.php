<?php

namespace Tourze\ProductCoreBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\FreightTemplateBundle\Entity\FreightTemplate;
use Tourze\ProductCoreBundle\Entity\Brand;
use Tourze\ProductCoreBundle\Entity\Price;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\SkuPackage;
use Tourze\ProductCoreBundle\Entity\Spu;

#[MenuProvider]
final readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        $ecommerceMenu = $item->getChild('电商中心');
        if (null === $ecommerceMenu) {
            $ecommerceMenu = $item->addChild('电商中心');
        }
        $ecommerceMenu->addChild('产品管理/SPU')->setUri($this->linkGenerator->getCurdListPage(Spu::class));
        $ecommerceMenu->addChild('商品规格/SKU')->setUri($this->linkGenerator->getCurdListPage(Sku::class));
        $ecommerceMenu->addChild('分类管理')->setUri($this->linkGenerator->getCurdListPage(Catalog::class));
        $ecommerceMenu->addChild('品牌管理')->setUri($this->linkGenerator->getCurdListPage(Brand::class));
        $ecommerceMenu->addChild('运费模板')->setUri($this->linkGenerator->getCurdListPage(FreightTemplate::class));
        $ecommerceMenu->addChild('价格管理')->setUri($this->linkGenerator->getCurdListPage(Price::class));
        $ecommerceMenu->addChild('SKU打包')->setUri($this->linkGenerator->getCurdListPage(SkuPackage::class));
    }
}
