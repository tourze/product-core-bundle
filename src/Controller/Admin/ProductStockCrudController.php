<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\Stock;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductStockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
    }
}
