<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\SkuAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSkuAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuAttribute::class;
    }
}
