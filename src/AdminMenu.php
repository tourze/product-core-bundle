<?php

namespace Tourze\ProductCoreBundle;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\ProductCoreBundle\Entity\Brand;
use Tourze\ProductCoreBundle\Entity\Category;
use Tourze\ProductCoreBundle\Entity\FreightTemplate;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Entity\StockLog;
use Tourze\ProductCoreBundle\Entity\Tag;

#[MenuProvider]
class AdminMenu
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('电商中心') === null) {
            $item->addChild('电商中心');
        }
        $item->getChild('电商中心')->addChild('产品/SPU')->setUri($this->linkGenerator->getCurdListPage(Spu::class));
        $item->getChild('电商中心')->addChild('商品/SKU')->setUri($this->linkGenerator->getCurdListPage(Sku::class));
        $item->getChild('电商中心')->addChild('分类管理')->setUri($this->linkGenerator->getCurdListPage(Category::class));
        $item->getChild('电商中心')->addChild('标签管理')->setUri($this->linkGenerator->getCurdListPage(Tag::class));
        $item->getChild('电商中心')->addChild('品牌管理')->setUri($this->linkGenerator->getCurdListPage(Brand::class));
        $item->getChild('电商中心')->addChild('库存日志')->setUri($this->linkGenerator->getCurdListPage(StockLog::class));
        $item->getChild('电商中心')->addChild('运费模板')->setUri($this->linkGenerator->getCurdListPage(FreightTemplate::class));
    }
}
