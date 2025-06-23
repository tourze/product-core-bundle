<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\StockLog;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductStockLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StockLog::class;
    }
}
