<?php

namespace ProductBundle\Controller\Admin;

use ProductBundle\Entity\Tag;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }
}
