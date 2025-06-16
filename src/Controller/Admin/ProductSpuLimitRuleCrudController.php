<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\SpuLimitRule;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuLimitRule::class;
    }
}
