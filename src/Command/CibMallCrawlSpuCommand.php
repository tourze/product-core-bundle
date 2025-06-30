<?php

namespace Tourze\ProductCoreBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use HttpClientBundle\Service\SmartHttpClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarExporter\VarExporter;
use Tourze\ProductCoreBundle\Entity\Price;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\SkuAttribute;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Entity\SpuAttribute;
use Tourze\ProductCoreBundle\Enum\PriceType;
use Tourze\ProductCoreBundle\Enum\SpuState;
use Tourze\ProductCoreBundle\Repository\PriceRepository;
use Tourze\ProductCoreBundle\Repository\SkuAttributeRepository;
use Tourze\ProductCoreBundle\Repository\SkuRepository;
use Tourze\ProductCoreBundle\Repository\SpuAttributeRepository;
use Tourze\ProductCoreBundle\Repository\SpuRepository;
use Yiisoft\Json\Json;

#[AsCommand(name: self::NAME, description: '采集兴业银行商城SPU数据')]
class CibMallCrawlSpuCommand extends Command
{
    public const NAME = 'product:cib-mall:crawl-spu';
    public function __construct(
        private readonly SmartHttpClient $httpClient,
        private readonly SpuRepository $spuRepository,
        private readonly SpuAttributeRepository $spuAttributeRepository,
        private readonly SkuRepository $skuRepository,
        private readonly SkuAttributeRepository $skuAttributeRepository,
        private readonly PriceRepository $priceRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $skuIds = [];

        $shopHome = $this->httpClient
            ->request('POST', 'https://mall.cib.com.cn/unified-mall-web/api/shopHome/getShopHome', [
                'body' => [
                    'json' => Json::encode((object) []),
                ],
            ])
            ->getContent();
        $shopHome = Json::decode($shopHome);
        $skuIds = array_merge($skuIds, explode(',', (string) $shopHome['goodsOne']['link']));

        $shopHomeBottom = $this->httpClient
            ->request('POST', 'https://mall.cib.com.cn/unified-mall-web/api/shopHome/getShopHomeBoottom', [
                'body' => [
                    'json' => Json::encode((object) []),
                ],
            ])
            ->getContent();
        $shopHomeBottom = Json::decode($shopHomeBottom);
        $skuIds = array_merge($skuIds, explode(',', (string) $shopHomeBottom['goodsTwo']['link']));
        $skuIds = array_merge($skuIds, explode(',', (string) $shopHomeBottom['goodsThree']['link']));
        $skuIds = array_merge($skuIds, explode(',', (string) $shopHomeBottom['goodsFour']['link']));

        $skuIds = array_unique($skuIds);
        $output->writeln('当前计划入库的SKU ID: ' . VarExporter::export($skuIds));

        foreach ($skuIds as $skuId) {
            $output->writeln("正在抓取：{$skuId}");
            $response = $this->httpClient->request('POST', 'https://mall.cib.com.cn/unified-mall-web/api/goods/getGoods', [
                'body' => [
                    'json' => Json::encode([
                        'skuId' => $skuId,
                    ]),
                ],
            ]);
            $response = $response->getContent();
            $response = Json::decode($response);
            $output->writeln(VarExporter::export($response));

            // 准备SPU
            $spuGTIN = "CIB-MALL-{$response['goodsSpu']['spuId']}";
            $spu = $this->spuRepository->findOneBy(['gtin' => $spuGTIN]);
            if ($spu === null) {
                $spu = new Spu();
                $spu->setGtin($spuGTIN);
            }

            $spu->setTitle($response['goodsSpu']['name']);
            $spu->setState(SpuState::ONLINE);
            // 描述
            $spuHtml = '';
            foreach ($response['goodsSpu']['picsList'] as $pic) {
                $spuHtml .= "<img src='https://file.cibfintech.com/{$pic}' alt=''>";
            }

            $spu->setContent($spuHtml);
            $spu->setValid(false);
            $this->entityManager->persist($spu);

            // SPU属性
            foreach ($response['goodsSpuPropertyCustom'] as $item) {
                if ('无' === $item['name']) {
                    continue;
                }

                $attr = $this->spuAttributeRepository->findOneBy([
                    'spu' => $spu,
                    'name' => $item['name'],
                ]);
                if ($attr === null) {
                    $attr = new SpuAttribute();
                    $attr->setSpu($spu);
                    $attr->setName($item['name']);
                    $attr->setValue($item['valueName']);
                    $this->entityManager->persist($attr);
                }
            }

            // 遍历SKU
            $spuThumbs = [];
            foreach ($response['goodsSkus'] as $item) {
                $skuGTIN = "{$spuGTIN}-{$item['skuId']}";
                $sku = $this->skuRepository->findOneBy([
                    'spu' => $spu,
                    'gtin' => $skuGTIN,
                ]);
                if ($sku === null) {
                    $sku = new Sku();
                    $sku->setSpu($spu);
                    $sku->setGtin($skuGTIN);
                }

                $sku->setUnit('件');

                // 图片
                $thumbs = [];
                foreach ($item['picsList'] as $pic) {
                    $spuThumbs[$pic] = [
                        'can_delete' => true,
                        'fileName' => '',
                        'id' => 0,
                        'name' => '',
                        'path' => "https://file.cibfintech.com/{$pic}",
                        'url' => "https://file.cibfintech.com/{$pic}",
                    ];
                    $thumbs[] = [
                        'can_delete' => true,
                        'fileName' => '',
                        'id' => 0,
                        'name' => '',
                        'path' => "https://file.cibfintech.com/{$pic}",
                        'url' => "https://file.cibfintech.com/{$pic}",
                    ];
                }

                $sku->setThumbs($thumbs !== [] ? $thumbs : null);
                $this->entityManager->persist($sku);

                // 价格
                $price = $this->priceRepository->findOneBy([
                    'sku' => $sku,
                    'type' => PriceType::SALE->value,
                ]);
                if ($price === null) {
                    $price = new Price();
                    $price->setSku($sku);
                    $price->setType(PriceType::SALE);
                }

                $price->setCurrency('CNY');
                $price->setPrice((string) ($item['price'] / 100));
                $price->setEffectTime(CarbonImmutable::now());
                $price->setExpireTime(CarbonImmutable::now()->addYears(100));
                $this->entityManager->persist($price);

                // 属性
                foreach ($response['goodsSpu']['propertysList'] as $j => $property) {
                    if (isset($item['propertysList'][$j])) {
                        $attribute = $this->skuAttributeRepository->findOneBy([
                            'sku' => $sku,
                            'name' => $property['name'],
                        ]);
                        if ($attribute === null) {
                            $attribute = new SkuAttribute();
                            $attribute->setSku($sku);
                            $attribute->setName($property['name']);
                        }

                        $attribute->setValue($item['propertysList'][$j]);
                        $this->entityManager->persist($attribute);
                    }
                }
            }

            $spuThumbs = array_values($spuThumbs);
            $spu->setThumbs($spuThumbs);
            $spu->setValid(true);
            $this->entityManager->persist($spu);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
