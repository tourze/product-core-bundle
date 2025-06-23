<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\SkuLimitRule;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSkuLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuLimitRule::class;
    }
}
