<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\Brand;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductBrandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Brand::class;
    }
}
