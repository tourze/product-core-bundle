<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\SkuLimitRule;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSkuLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuLimitRule::class;
    }
}
