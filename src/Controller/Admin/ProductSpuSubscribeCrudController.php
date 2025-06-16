<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\SpuSubscribe;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuSubscribeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuSubscribe::class;
    }
}
