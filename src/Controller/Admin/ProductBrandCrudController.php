<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\Brand;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductBrandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Brand::class;
    }
}
