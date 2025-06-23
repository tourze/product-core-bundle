<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\Tag;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }
}
