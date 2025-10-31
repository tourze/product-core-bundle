<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\FileStorageBundle\Field\ImageGalleryMultiField;
use Tourze\ProductCoreBundle\Entity\Sku;

#[AdminCrud(routePath: '/product/sku', routeName: 'product_sku')]
final class ProductSkuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sku::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SKU')
            ->setEntityLabelInPlural('SKU管理')
            ->setPageTitle('index', 'SKU列表')
            ->setPageTitle('new', '创建SKU')
            ->setPageTitle('edit', '编辑SKU')
            ->setPageTitle('detail', 'SKU详情')
            ->setHelp('index', '管理产品SKU信息，包括编码、规格属性和库存状态')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title', 'gtin', 'mpn'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield AssociationField::new('spu', '所属SPU');
        yield TextField::new('title', '规格标题')
            ->setHelp('自定义规格显示名称，留空时将自动生成')
            ->hideOnIndex()
        ;
        // 多图选择：SKU 图集
        yield ImageGalleryMultiField::new('thumbs', '图片集');
        yield TextField::new('gtin', 'GTIN编码')->setHelp('全球唯一编码');
        yield TextField::new('mpn', 'MPN编码')->setHelp('厂商型号码');
        yield TextField::new('unit', '单位')->setHelp('销售单位');
        yield BooleanField::new('needConsignee', '需要收货');
        yield TextareaField::new('remark', '备注');
        yield MoneyField::new('marketPrice', '市场价')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('商品市场价格')
            ->hideOnIndex()
        ;
        yield MoneyField::new('costPrice', '成本价')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('商品成本价格')
            ->hideOnIndex()
        ;
        yield MoneyField::new('originalPrice', '原价')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('商品原价')
            ->hideOnIndex()
        ;
        yield IntegerField::new('salesReal', '真实销量');
        yield IntegerField::new('salesVirtual', '虚拟销量');
        yield BooleanField::new('valid', '上架状态');
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
            ->add(EntityFilter::new('spu', '所属SPU'))
            ->add(TextFilter::new('gtin', 'GTIN编码'))
            ->add(TextFilter::new('mpn', 'MPN编码'))
            ->add(BooleanFilter::new('needConsignee', '需要收货'))
            ->add(BooleanFilter::new('valid', '上架状态'))
        ;
    }
}
