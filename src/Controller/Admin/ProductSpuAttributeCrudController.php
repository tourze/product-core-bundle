<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\SpuAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuAttribute::class;
    }
}
