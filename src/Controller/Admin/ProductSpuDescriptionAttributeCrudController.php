<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\SpuDescriptionAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuDescriptionAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuDescriptionAttribute::class;
    }
}
