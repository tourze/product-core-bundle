<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\Sku;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSkuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sku::class;
    }
}
