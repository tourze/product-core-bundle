<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\SkuAttribute;

class ProductSkuAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuAttribute::class;
    }
}
