<?php

namespace ProductCoreBundle;

use Knp\Menu\ItemInterface;
use ProductCoreBundle\Entity\Brand;
use ProductCoreBundle\Entity\Category;
use ProductCoreBundle\Entity\FreightTemplate;
use ProductCoreBundle\Entity\Sku;
use ProductCoreBundle\Entity\Spu;
use ProductCoreBundle\Entity\StockLog;
use ProductCoreBundle\Entity\Tag;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

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
