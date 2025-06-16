<?php

namespace ProductBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

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
#[AsPermission(title: '产品模块')]
class ProductBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle::class => ['all' => true],
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class => ['all' => true],
            \Tourze\Symfony\CronJob\CronJobBundle::class => ['all' => true],
            \AntdCpBundle\AntdCpBundle::class => ['all' => true],
            \StoreBundle\StoreBundle::class => ['all' => true],
        ];
    }
}
