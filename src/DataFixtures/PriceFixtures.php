<?php

namespace Tourze\ProductCoreBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\ProductCoreBundle\Entity\Price;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Enum\PriceType;

#[When(env: 'test')]
#[When(env: 'dev')]
final class PriceFixtures extends Fixture implements DependentFixtureInterface
{
    public const TEST_PRICE_REFERENCE = 'test-price';

    public function load(ObjectManager $manager): void
    {
        $sku = $this->getReference(BasicSpuFixtures::BASIC_SKU_REFERENCE, Sku::class);

        $price = new Price();
        $price->setSku($sku);
        $price->setType(PriceType::SALE);
        $price->setCurrency('CNY');
        $price->setPrice('99.00');
        $price->setEffectTime(CarbonImmutable::now());
        $price->setCreateTime(CarbonImmutable::now());
        $price->setUpdateTime(CarbonImmutable::now());

        $manager->persist($price);
        $this->addReference(self::TEST_PRICE_REFERENCE, $price);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BasicSpuFixtures::class];
    }
}
