<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\SpuSubscribe;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuSubscribeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuSubscribe::class;
    }
}
