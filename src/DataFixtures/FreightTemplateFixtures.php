<?php

namespace Tourze\ProductCoreBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\FreightTemplateBundle\Entity\FreightTemplate;
use Tourze\FreightTemplateBundle\Enum\FreightValuationType;
use Tourze\ProductCoreBundle\Enum\DeliveryType;

#[When(env: 'test')]
#[When(env: 'dev')]
final class FreightTemplateFixtures extends Fixture
{
    public const TEST_FREIGHT_TEMPLATE_REFERENCE = 'test-freight-template';

    public function load(ObjectManager $manager): void
    {
        $template = new FreightTemplate();
        $template->setName('测试运费模板');
        $template->setDeliveryType(DeliveryType::EXPRESS);
        $template->setValuationType(FreightValuationType::FIXED);
        $template->setCurrency('CNY');
        $template->setValid(true);
        $template->setCreateTime(CarbonImmutable::now());
        $template->setUpdateTime(CarbonImmutable::now());

        $manager->persist($template);
        $this->addReference(self::TEST_FREIGHT_TEMPLATE_REFERENCE, $template);

        $manager->flush();
    }
}
