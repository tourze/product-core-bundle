<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\SpuLimitRule;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuLimitRule::class;
    }
}
