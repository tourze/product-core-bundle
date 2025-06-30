<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\SkuPackage;

class ProductSkuPackageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuPackage::class;
    }
}
