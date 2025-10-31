<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\ProductDetailResponse;

/**
 * @internal
 */
#[CoversClass(ProductDetailResponse::class)]
final class ProductDetailResponseTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $response = new ProductDetailResponse(
            id: 1,
            gtin: null,
            title: 'Test Product',
            subtitle: null,
            type: null,
            brand: null,
            mainPic: null,
            mainThumb: '',
            images: [],
            content: null,
            valid: true,
            audited: true,
            autoReleaseTime: null,
            createTime: null,
            updateTime: null,
            skus: [],
            attributes: [],
            descriptionAttributes: [],
            categories: [],
            salePrices: null,
            displaySalePrice: null,
            displayTaxPrice: null,
        );

        $this->assertInstanceOf(ProductDetailResponse::class, $response);
        $this->assertSame(1, $response->id);
        $this->assertSame('Test Product', $response->title);
        $this->assertTrue($response->valid);
        $this->assertTrue($response->audited);
    }
}
