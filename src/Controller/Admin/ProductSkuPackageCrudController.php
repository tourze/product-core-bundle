<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\SkuPackage;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSkuPackageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuPackage::class;
    }
}
