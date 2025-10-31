<?php

namespace Tourze\ProductCoreBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;

#[When(env: 'test')]
#[When(env: 'dev')]
final class SkuFixtures extends Fixture implements DependentFixtureInterface
{
    public const TEST_SKU_REFERENCE = 'test-sku-additional';

    public function load(ObjectManager $manager): void
    {
        $spu = $this->getReference(BasicSpuFixtures::BASIC_SPU_REFERENCE, Spu::class);

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setGtin('TEST_SKU_001');
        $sku->setUnit('个');
        $sku->setNeedConsignee(true);
        $sku->setThumbs([['url' => '/images/test-sku-thumb.png']]);
        $sku->setCreateTime(CarbonImmutable::now());
        $sku->setUpdateTime(CarbonImmutable::now());

        $manager->persist($sku);
        $this->addReference(self::TEST_SKU_REFERENCE, $sku);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BasicSpuFixtures::class];
    }
}
