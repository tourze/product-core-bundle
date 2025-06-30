<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\Stock;

class ProductStockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
    }
}
