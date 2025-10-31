<?php

namespace Tourze\ProductCoreBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductAttributeBundle\Entity\SkuAttribute;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Service\ProductArrayFormatterService;

/**
 * @internal
 */
#[CoversClass(ProductArrayFormatterService::class)]
#[RunTestsInSeparateProcesses]
final class ProductArrayFormatterServiceTest extends AbstractIntegrationTestCase
{
    private ProductArrayFormatterService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(ProductArrayFormatterService::class);
    }

    public function testFormatSpuArray(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test Product');
        $spu->setSubtitle('Test Subtitle');
        $spu->setGtin('1234567890');
        $spu->setMainPic('https://example.com/main.jpg');
        $spu->setThumbs([['url' => 'https://example.com/thumb.jpg']]);
        $spu->setContent('Test content');

        $result = $this->service->formatSpuArray($spu);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('subtitle', $result);
        $this->assertArrayHasKey('gtin', $result);
        $this->assertArrayHasKey('mainPic', $result);
        $this->assertArrayHasKey('thumbs', $result);
        $this->assertArrayHasKey('content', $result);
        $this->assertArrayHasKey('skus', $result);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertArrayHasKey('mainThumb', $result);

        $this->assertEquals('Test Product', $result['title']);
        $this->assertEquals('Test Subtitle', $result['subtitle']);
        $this->assertEquals('1234567890', $result['gtin']);
        $this->assertEquals('https://example.com/main.jpg', $result['mainPic']);
        $this->assertEquals('Test content', $result['content']);
    }

    public function testFormatSpuCheckoutArray(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test Product');
        $spu->setGtin('1234567890');

        $result = $this->service->formatSpuCheckoutArray($spu);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('gtin', $result);
        $this->assertArrayNotHasKey('skus', $result);
        $this->assertArrayNotHasKey('attributes', $result);
    }

    public function testFormatSkuArray(): void
    {
        $sku = new Sku();
        $sku->setGtin('SKU123');
        $sku->setUnit('件');
        $sku->setValid(true);
        $sku->setSalesReal(10);
        $sku->setSalesVirtual(5);

        $result = $this->service->formatSkuArray($sku);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('gtin', $result);
        $this->assertArrayHasKey('unit', $result);
        $this->assertArrayHasKey('valid', $result);
        $this->assertArrayHasKey('salesCount', $result);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertArrayHasKey('marketPrice', $result);
        $this->assertArrayHasKey('costPrice', $result);
        $this->assertArrayHasKey('originalPrice', $result);

        $this->assertEquals('SKU123', $result['gtin']);
        $this->assertEquals('件', $result['unit']);
        $this->assertTrue($result['valid']);
        $this->assertEquals(15, $result['salesCount']);
    }

    public function testFormatSkuCheckoutArray(): void
    {
        $sku = new Sku();
        $sku->setGtin('SKU123');
        $sku->setUnit('件');

        $result = $this->service->formatSkuCheckoutArray($sku);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('gtin', $result);
        $this->assertArrayHasKey('unit', $result);
        $this->assertArrayHasKey('valid', $result);
    }

    public function testGetSalePrice(): void
    {
        $sku = new Sku();
        $sku->setMarketPrice('100.00');

        $result = $this->service->getSalePrice($sku);

        $this->assertEquals('100.00', $result);
    }

    public function testGetCostPrice(): void
    {
        $sku = new Sku();
        $sku->setCostPrice('50.00');

        $result = $this->service->getCostPrice($sku);

        $this->assertEquals('50.00', $result);
    }

    public function testGetOriginalPrice(): void
    {
        $sku = new Sku();
        $sku->setOriginalPrice('150.00');

        $result = $this->service->getOriginalPrice($sku);

        $this->assertEquals('150.00', $result);
    }

    public function testGetSpuMainThumbWithMainPic(): void
    {
        $spu = new Spu();
        $spu->setMainPic('https://example.com/main.jpg');

        $result = $this->service->getSpuMainThumb($spu);

        $this->assertEquals('https://example.com/main.jpg', $result);
    }

    public function testGetSpuMainThumbWithThumbs(): void
    {
        $spu = new Spu();
        $spu->setMainPic('');
        $spu->setThumbs([['url' => 'https://example.com/thumb.jpg']]);

        $result = $this->service->getSpuMainThumb($spu);

        $this->assertEquals('https://example.com/thumb.jpg', $result);
    }

    public function testGetSpuMainThumbEmpty(): void
    {
        $spu = new Spu();
        $spu->setMainPic('');
        $spu->setThumbs([]);

        $result = $this->service->getSpuMainThumb($spu);

        $this->assertEquals('', $result);
    }

    public function testSkuDisplayAttributeGeneration(): void
    {
        $sku = new Sku();
        $sku->setGtin('SKU123');

        $attribute1 = new SkuAttribute();
        $attribute1->setName('color');
        $attribute1->setValue('red');

        $attribute2 = new SkuAttribute();
        $attribute2->setName('size');
        $attribute2->setValue('L');

        $sku->addAttribute($attribute1);
        $sku->addAttribute($attribute2);

        $result = $this->service->formatSkuArray($sku);

        $this->assertArrayHasKey('displayAttribute', $result);
        $this->assertIsString($result['displayAttribute']);
        $this->assertStringContainsString('color', $result['displayAttribute']);
        $this->assertStringContainsString('size', $result['displayAttribute']);
    }

    public function testSkuDisplayAttributeFiltersInternalAttributes(): void
    {
        $sku = new Sku();
        $sku->setGtin('SKU123');

        $internalAttribute = new SkuAttribute();
        $internalAttribute->setName('itemCode');
        $internalAttribute->setValue('internal');

        $visibleAttribute = new SkuAttribute();
        $visibleAttribute->setName('color');
        $visibleAttribute->setValue('blue');

        $sku->addAttribute($internalAttribute);
        $sku->addAttribute($visibleAttribute);

        $result = $this->service->formatSkuArray($sku);

        $this->assertArrayHasKey('displayAttribute', $result);
        $this->assertIsString($result['displayAttribute']);
        $this->assertStringNotContainsString('itemCode', $result['displayAttribute']);
        $this->assertStringContainsString('color', $result['displayAttribute']);
    }

    public function testSkuDisplayAttributeFallbackToGtin(): void
    {
        $sku = new Sku();
        $sku->setGtin('SKU123');

        $result = $this->service->formatSkuArray($sku);

        $this->assertArrayHasKey('displayAttribute', $result);
        $this->assertEquals('SKU123', $result['displayAttribute']);
    }

    public function testFormatSkuForSpuArray(): void
    {
        $sku = new Sku();
        $sku->setGtin('SKU456');
        $sku->setUnit('个');
        $sku->setValid(true);
        $sku->setNeedConsignee(true);
        $sku->setMarketPrice('199.99');
        $sku->setCostPrice('99.99');
        $sku->setOriginalPrice('299.99');
        $sku->setSalesReal(25);
        $sku->setSalesVirtual(75);
        $sku->setThumbs([['url' => 'https://example.com/sku-thumb.jpg']]);

        $attribute = new SkuAttribute();
        $attribute->setName('color');
        $attribute->setValue('green');
        $sku->addAttribute($attribute);

        $result = $this->service->formatSkuForSpuArray($sku);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('gtin', $result);
        $this->assertArrayHasKey('unit', $result);
        $this->assertArrayHasKey('needConsignee', $result);
        $this->assertArrayHasKey('thumbs', $result);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertArrayHasKey('valid', $result);
        $this->assertArrayHasKey('displayAttribute', $result);
        $this->assertArrayHasKey('marketPrice', $result);
        $this->assertArrayHasKey('costPrice', $result);
        $this->assertArrayHasKey('originalPrice', $result);
        $this->assertArrayHasKey('mainThumb', $result);
        $this->assertArrayHasKey('salesCount', $result);

        $this->assertEquals('SKU456', $result['gtin']);
        $this->assertEquals('个', $result['unit']);
        $this->assertTrue($result['valid']);
        $this->assertTrue($result['needConsignee']);
        $this->assertEquals('199.99', $result['marketPrice']);
        $this->assertEquals('99.99', $result['costPrice']);
        $this->assertEquals('299.99', $result['originalPrice']);
        $this->assertEquals(100, $result['salesCount']); // 25 + 75
        $this->assertEquals('colorgreen', $result['displayAttribute']);
        $this->assertEquals('https://example.com/sku-thumb.jpg', $result['mainThumb']);
        $this->assertIsArray($result['attributes']);
        $this->assertCount(1, $result['attributes']);
    }

    public function testFormatSkuForSpuArrayWithSpuFallbackThumb(): void
    {
        $spu = new Spu();
        $spu->setMainPic('https://example.com/spu-main.jpg');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setGtin('SKU789');
        $sku->setUnit('个');
        $sku->setThumbs([]); // 空缩略图，应该回退到SPU的主图

        $result = $this->service->formatSkuForSpuArray($sku);

        $this->assertArrayHasKey('mainThumb', $result);
        $this->assertEquals('https://example.com/spu-main.jpg', $result['mainThumb']);
    }
}
