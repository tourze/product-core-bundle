<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\SpuAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuAttribute::class;
    }
}
