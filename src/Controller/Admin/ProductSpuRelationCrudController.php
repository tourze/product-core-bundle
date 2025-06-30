<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\SpuRelation;

class ProductSpuRelationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuRelation::class;
    }
}
