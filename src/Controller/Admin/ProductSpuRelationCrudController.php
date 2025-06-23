<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\SpuRelation;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuRelationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuRelation::class;
    }
}
