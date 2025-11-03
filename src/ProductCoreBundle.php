<?php

namespace Tourze\ProductCoreBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\CatalogBundle\CatalogBundle;
use Tourze\DoctrineEntityLockBundle\DoctrineEntityLockBundle;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineResolveTargetEntityBundle\DependencyInjection\Compiler\ResolveTargetEntityPass;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\JsonRPCPaginatorBundle\JsonRPCPaginatorBundle;
use Tourze\ProductAttributeBundle\ProductAttributeBundle;
use Tourze\ProductServiceContracts\SKU;
use Tourze\ProductServiceContracts\SPU;
use Tourze\TagManageBundle\TagManageBundle;

/**
 * 商品模块
 *
 * 需要补充一些概念的实现：
 *
 * 货龄。参考：
 * https://www.lokad.com/cn/fifo-%E5%BA%93%E5%AD%98-%E6%96%B9%E6%B3%95
 * https://my.oschina.net/yonge/blog/84
 * https://www.zhihu.com/question/22961004
 *
 * 店铺动销率 = 有销量的宝贝数量 / 店铺所有宝贝的总数 * 100%
 * 宝贝动销率 = 已销售的宝贝销量 / 宝贝的总库存 * 100%
 * 参考：https://www.icoa.cn/a/808.html
 *
 * 退货率 = ?
 * 缺货率 =（缺货次数/顾客订货次数）×100%
 * 准时交货率=（准时交货次数/总交货次数）×100%
 * 参考：
 * 1. http://wemedia.ifeng.com/54473473/wemedia.shtml
 * 2. https://www.jianshu.com/p/2fc35c371207
 *
 * @see https://zhuanlan.zhihu.com/p/158010653
 * @see https://www.iepgf.cn/thread-378608-1-1.html
 * @see https://www.cnblogs.com/purple5252/p/14597599.html
 * @see https://xie.infoq.cn/article/d70abaebdc1db54681a741729
 * @see https://learnku.com/articles/21623
 */
final class ProductCoreBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            DoctrineSnowflakeBundle::class => ['all' => true],
            DoctrineIndexedBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            JsonRPCPaginatorBundle::class => ['all' => true],
            DoctrineEntityLockBundle::class => ['all' => true],
            CatalogBundle::class => ['all' => true],
            TagManageBundle::class => ['all' => true],
            ProductAttributeBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new ResolveTargetEntityPass(SPU::class, Entity\Spu::class),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1000,
        );

        $container->addCompilerPass(
            new ResolveTargetEntityPass(SKU::class, Entity\Sku::class),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1000,
        );
    }
}
