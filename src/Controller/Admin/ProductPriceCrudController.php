<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\Price;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductPriceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Price::class;
    }
}
