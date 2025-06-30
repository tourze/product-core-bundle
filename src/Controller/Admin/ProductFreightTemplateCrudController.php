<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use Tourze\ProductCoreBundle\Entity\FreightTemplate;

class ProductFreightTemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FreightTemplate::class;
    }
}
