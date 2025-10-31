<?php

namespace Tourze\ProductCoreBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\SkuPackage;
use Tourze\ProductCoreBundle\Enum\PackageType;

#[AdminCrud(routePath: '/product/sku-package', routeName: 'product_sku_package')]
final class ProductSkuPackageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuPackage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SKU打包')
            ->setEntityLabelInPlural('SKU打包管理')
            ->setPageTitle('index', 'SKU打包列表')
            ->setPageTitle('new', '创建SKU打包')
            ->setPageTitle('edit', '编辑SKU打包')
            ->setPageTitle('detail', 'SKU打包详情')
            ->setHelp('index', '管理SKU打包配置，包括打包类型、数量和备注信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'value'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield AssociationField::new('sku', '所属SKU');
        yield ChoiceField::new('type', '打包类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => PackageType::class,
                'choice_label' => fn (PackageType $choice) => $choice->getLabel(),
            ])
            ->formatValue(fn ($value) => $value?->getLabel())
        ;
        yield TextField::new('value', '属性值');
        yield IntegerField::new('quantity', '数量');
        yield TextareaField::new('remark', '备注');
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
            ->add(EntityFilter::new('sku', '所属SKU'))
            ->add(ChoiceFilter::new('type', '打包类型')->setChoices([
                '优惠券' => PackageType::COUPON,
            ]))
            ->add(TextFilter::new('value', '属性值'))
        ;
    }
}
