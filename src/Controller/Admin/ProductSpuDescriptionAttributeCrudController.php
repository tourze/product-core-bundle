<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\SpuDescriptionAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuDescriptionAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuDescriptionAttribute::class;
    }
}
