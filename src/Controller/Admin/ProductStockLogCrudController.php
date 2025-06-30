<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\StockLog;

class ProductStockLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StockLog::class;
    }
}
