<?php

namespace Tourze\ProductCoreBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\ProductCoreBundle\Entity\Brand;

#[When(env: 'test')]
#[When(env: 'dev')]
final class BrandFixtures extends Fixture
{
    public const TEST_BRAND_REFERENCE = 'test-brand';

    public function load(ObjectManager $manager): void
    {
        $brand = new Brand();
        $brand->setName('测试品牌');
        $brand->setValid(true);
        $brand->setLogoUrl('/images/test-brand-logo.png');
        $brand->setCreateTime(CarbonImmutable::now());
        $brand->setUpdateTime(CarbonImmutable::now());

        $manager->persist($brand);
        $this->addReference(self::TEST_BRAND_REFERENCE, $brand);

        $manager->flush();
    }
}
