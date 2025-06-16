<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\CategoryLimitRule;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductCategoryLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryLimitRule::class;
    }
}
