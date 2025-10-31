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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Tourze\ProductCoreBundle\Entity\Price;
use Tourze\ProductCoreBundle\Enum\PriceType;

#[AdminCrud(routePath: '/product/price', routeName: 'product_price')]
final class ProductPriceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Price::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('价格')
            ->setEntityLabelInPlural('价格管理')
            ->setPageTitle('index', '价格列表')
            ->setPageTitle('new', '创建价格')
            ->setPageTitle('edit', '编辑价格')
            ->setPageTitle('detail', '价格详情')
            ->setHelp('index', '管理商品价格信息，包括销售价格、成本价格等不同类型的价格')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'type', 'currency'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        //        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield AssociationField::new('sku', '规格');
        yield ChoiceField::new('type', '价格类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => PriceType::class])
            ->formatValue(function ($value) {
                return $value instanceof PriceType ? $value->getLabel() : '';
            })
        ;
        yield TextField::new('currency', '币种');
        yield MoneyField::new('price', '价格')->setCurrency('CNY');
        yield NumberField::new('taxRate', '税率(%)')->hideOnForm();
        yield TextField::new('formula', '公式')->hideOnForm();
        yield IntegerField::new('priority', '优先级')->hideOnForm();
        yield IntegerField::new('minBuyQuantity', '最小购买数量')->hideOnIndex();
        yield DateTimeField::new('effectTime', '生效时间');
        yield DateTimeField::new('expireTime', '过期时间');
        yield BooleanField::new('canRefund', '允许退款')->hideOnForm();
        yield BooleanField::new('isDefault', '是否默认');
        yield TextField::new('remark', '备注');
        yield TextareaField::new('description', '描述');
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
        $priceTypeChoices = [];
        foreach (PriceType::cases() as $case) {
            $priceTypeChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('sku', 'SKU'))
            ->add(ChoiceFilter::new('type', '价格类型')->setChoices($priceTypeChoices))
            ->add(TextFilter::new('currency', '币种'))
            ->add(BooleanFilter::new('canRefund', '允许退款'))
            ->add(BooleanFilter::new('isDefault', '是否默认'))
            ->add(DateTimeFilter::new('effectTime', '生效时间'))
            ->add(DateTimeFilter::new('expireTime', '过期时间'))
        ;
    }
}
