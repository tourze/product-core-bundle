<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\SpuDescriptionAttribute;

class ProductSpuDescriptionAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuDescriptionAttribute::class;
    }
}
