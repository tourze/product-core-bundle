<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\Price;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductPriceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Price::class;
    }
}
