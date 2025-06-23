<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\Stock;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductStockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
    }
}
