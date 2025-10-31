<?php

namespace Tourze\ProductCoreBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Enum\SpuState;

#[When(env: 'test')]
#[When(env: 'dev')]
final class SpuFixtures extends Fixture
{
    public const TEST_SPU_REFERENCE = 'test-spu-additional';

    public function load(ObjectManager $manager): void
    {
        $spu = new Spu();
        $spu->setTitle('测试产品SPU');
        $spu->setState(SpuState::ONLINE);
        $spu->setGtin('TEST_SPU_001');
        $spu->setThumbs([['url' => '/images/test-spu-thumb.png']]);
        $spu->setCreateTime(CarbonImmutable::now());
        $spu->setUpdateTime(CarbonImmutable::now());

        $manager->persist($spu);
        $this->addReference(self::TEST_SPU_REFERENCE, $spu);

        $manager->flush();
    }
}
