<?php

namespace Tourze\ProductCoreBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\SkuPackage;
use Tourze\ProductCoreBundle\Enum\PackageType;

#[When(env: 'test')]
#[When(env: 'dev')]
final class SkuPackageFixtures extends Fixture implements DependentFixtureInterface
{
    public const TEST_SKU_PACKAGE_REFERENCE = 'test-sku-package';

    public function load(ObjectManager $manager): void
    {
        $sku = $this->getReference(BasicSpuFixtures::BASIC_SKU_REFERENCE, Sku::class);

        $package = new SkuPackage();
        $package->setSku($sku);
        $package->setType(PackageType::COUPON);
        $package->setValue('weight:1.50kg|dimensions:20x15x10cm');
        $package->setCreateTime(CarbonImmutable::now());
        $package->setUpdateTime(CarbonImmutable::now());

        $manager->persist($package);
        $this->addReference(self::TEST_SKU_PACKAGE_REFERENCE, $package);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BasicSpuFixtures::class];
    }
}
