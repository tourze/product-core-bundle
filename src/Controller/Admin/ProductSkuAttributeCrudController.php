<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\SkuAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSkuAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuAttribute::class;
    }
}
