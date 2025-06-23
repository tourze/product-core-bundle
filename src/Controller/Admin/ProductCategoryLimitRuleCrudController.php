<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\CategoryLimitRule;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductCategoryLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryLimitRule::class;
    }
}
