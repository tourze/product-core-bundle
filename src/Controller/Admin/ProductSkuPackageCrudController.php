<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\SkuPackage;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSkuPackageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuPackage::class;
    }
}
