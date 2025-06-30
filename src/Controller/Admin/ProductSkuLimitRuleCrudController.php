<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\SkuLimitRule;

class ProductSkuLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuLimitRule::class;
    }
}
