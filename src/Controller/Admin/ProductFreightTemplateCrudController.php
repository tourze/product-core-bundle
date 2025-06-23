<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\FreightTemplate;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductFreightTemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FreightTemplate::class;
    }
}
