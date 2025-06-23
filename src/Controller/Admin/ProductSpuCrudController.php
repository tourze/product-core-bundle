<?php

namespace ProductCoreBundle\Controller\Admin;

use ProductCoreBundle\Entity\Spu;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class ProductSpuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Spu::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
