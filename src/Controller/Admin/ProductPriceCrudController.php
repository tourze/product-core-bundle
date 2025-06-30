<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\Price;

class ProductPriceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Price::class;
    }
}
