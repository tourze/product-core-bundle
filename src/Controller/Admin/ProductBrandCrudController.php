<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\ProductCoreBundle\Entity\Brand;

#[AdminCrud(routePath: '/product/brand', routeName: 'product_brand')]
final class ProductBrandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Brand::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('品牌')
            ->setEntityLabelInPlural('品牌管理')
            ->setPageTitle('index', '品牌列表')
            ->setPageTitle('new', '创建品牌')
            ->setPageTitle('edit', '编辑品牌')
            ->setPageTitle('detail', '品牌详情')
            ->setHelp('index', '管理产品品牌信息，包括品牌名称、Logo和状态')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield TextField::new('name', '品牌名称');
        yield UrlField::new('logoUrl', 'Logo地址');
        yield BooleanField::new('valid', '是否有效');
        yield DateTimeField::new('createTime', '创建时间')->hideOnForm();
        yield DateTimeField::new('updateTime', '更新时间')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '品牌名称'))
            ->add(BooleanFilter::new('valid', '是否有效'))
        ;
    }
}
