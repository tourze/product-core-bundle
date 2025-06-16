<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\FreightTemplate;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductFreightTemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FreightTemplate::class;
    }
}
