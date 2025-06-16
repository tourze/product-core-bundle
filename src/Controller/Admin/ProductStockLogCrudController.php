<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\StockLog;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductStockLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StockLog::class;
    }
}
