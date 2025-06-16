<?php

namespace ProductBundle\DataFixtures;

use Carbon\Carbon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use ProductBundle\Entity\Sku;
use ProductBundle\Entity\Spu;
use ProductBundle\Enum\SpuState;
use ProductBundle\Repository\SkuRepository;
use ProductBundle\Repository\SpuRepository;

class BasicSpuFixture extends Fixture
{
    public function __construct(
        private readonly SpuRepository $spuRepository,
        private readonly SkuRepository $skuRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 保存SPU
        $spu = $this->spuRepository->findOneBy(['title' => '比熊犬成犬粮']);
        if (!$spu) {
            $spu = new Spu();
            $spu->setTitle('比熊犬成犬粮');
            $spu->setState(SpuState::ONLINE);
            $spu->setGtin('05f2269a2f0f4392a0ef6fcc6fc76fcd');
            $spu->setThumbs([
                ['url' => 'https://arvatorc.blob.core.chinacloudapi.cn/rcminipicture/pc15602380156089.jpg'],
            ]);
            $spu->setCreateTime(Carbon::now());
        }

        $spu->setUpdateTime(Carbon::now());
        $manager->persist($spu);

        // 保存SKU
        $skus = $this->skuRepository->findBy(['spu' => $spu]);
        if (empty($skus)) {
            $sku = new Sku();
            $sku->setSpu($spu);
            $sku->setGtin('14610300');
            $sku->setCreateTime(Carbon::now());
            $sku->setUnit('个');
            $sku->setThumbs([
                ['url' => 'https://arvatorc.blob.core.chinacloudapi.cn/rcminipicture/pc15602380105326.jpg'],
            ]);
            $sku->setNeedConsignee(true);
            $manager->persist($sku);
        }

        $manager->flush();
    }
}
