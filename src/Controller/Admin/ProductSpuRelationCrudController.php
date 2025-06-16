<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\SpuRelation;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuRelationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuRelation::class;
    }
}
