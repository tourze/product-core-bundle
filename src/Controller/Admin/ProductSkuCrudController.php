<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\Sku;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSkuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sku::class;
    }
}
