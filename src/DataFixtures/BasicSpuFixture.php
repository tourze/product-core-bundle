<?php

namespace Tourze\ProductCoreBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Enum\SpuState;

class BasicSpuFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建 SPU
        $spu = new Spu();
        $spu->setTitle('比熊犬成犬粮');
        $spu->setState(SpuState::ONLINE);
        $spu->setGtin('05f2269a2f0f4392a0ef6fcc6fc76fcd');
        $spu->setThumbs([
            ['url' => 'https://arvatorc.blob.core.chinacloudapi.cn/rcminipicture/pc15602380156089.jpg'],
        ]);
        $spu->setCreateTime(CarbonImmutable::now());
        $spu->setUpdateTime(CarbonImmutable::now());
        $manager->persist($spu);

        // 设置引用，供其他 fixture 使用
        $this->addReference('basic-spu', $spu);

        // 创建 SKU
        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setGtin('14610300');
        $sku->setCreateTime(CarbonImmutable::now());
        $sku->setUnit('个');
        $sku->setThumbs([
            ['url' => 'https://arvatorc.blob.core.chinacloudapi.cn/rcminipicture/pc15602380105326.jpg'],
        ]);
        $sku->setNeedConsignee(true);
        $manager->persist($sku);

        // 设置引用，供其他 fixture 使用
        $this->addReference('basic-sku', $sku);

        $manager->flush();
    }
}
