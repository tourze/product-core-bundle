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
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\FileStorageBundle\Field\ImageGalleryField;
use Tourze\FileStorageBundle\Field\ImageGalleryMultiField;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Enum\SpuState;
use Tourze\TagManageBundle\Entity\Tag;

#[AdminCrud(routePath: '/product/spu', routeName: 'product_spu')]
final class ProductSpuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Spu::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SPU')
            ->setEntityLabelInPlural('SPU管理')
            ->setPageTitle('index', 'SPU列表')
            ->setPageTitle('new', '创建SPU')
            ->setPageTitle('edit', '编辑SPU')
            ->setPageTitle('detail', 'SPU详情')
            ->setHelp('index', '管理产品SPU信息，包括标题、品牌、状态和发布时间')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title', 'gtin', 'type'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->setFormTypeOption('mapped', false)
            ->hideOnForm()
        ;

        yield TextField::new('gtin', 'GTIN编码')->setHelp('唯一编码');
        yield ImageGalleryField::new('mainPic', '主图')
            ->setHelp('封面图')
        ;
        yield ImageGalleryMultiField::new('thumbs', '轮播图')
            ->setHelp('支持多图上传，拖动图片可以排序')->hideOnIndex()
        ;
        yield TextField::new('title', '标题');
        yield TextField::new('type', '类型');
        yield AssociationField::new('brand', '品牌')
            ->setHelp('选择商品品牌')
            ->hideOnIndex()
        ;
        yield TextField::new('subtitle', '副标题')->hideOnIndex();
        yield ChoiceField::new('state', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => SpuState::class,
                'choice_label' => fn (SpuState $choice) => $choice->getLabel(),
            ])
            ->formatValue(fn ($value) => $value?->getLabel())
            ->setHelp('商品状态：上架/下架等')
        ;

        yield AssociationField::new('categories', '分类')
            ->setHelp('选择商品所属分类，支持多选')
            ->hideOnIndex()
        ;
        yield AssociationField::new('tags', '标签')
            ->setHelp('选择商品标签，支持多选')
            ->hideOnIndex()
        ;
        yield TextareaField::new('content', '描述')->hideOnIndex();
        yield TextareaField::new('remark', '备注')->hideOnIndex();
        yield BooleanField::new('valid', '上架状态');
        yield IntegerField::new('sortNumber', '排序')->hideOnIndex();
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
            ->add(TextFilter::new('title', '标题'))
            ->add(TextFilter::new('gtin', 'GTIN编码'))
            ->add(TextFilter::new('type', '类型'))
            ->add(EntityFilter::new('brand', '品牌'))
            ->add(ChoiceFilter::new('state', '状态')->setChoices([
                '上架中' => SpuState::ONLINE,
                '已下架' => SpuState::OFFLINE,
            ]))
            ->add(EntityFilter::new('categories', '分类'))
            ->add(EntityFilter::new('tags', '标签'))
            ->add(BooleanFilter::new('valid', '上架状态'))
        ;
    }
}
