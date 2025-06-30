<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\CategoryLimitRule;

class ProductCategoryLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryLimitRule::class;
    }
}
