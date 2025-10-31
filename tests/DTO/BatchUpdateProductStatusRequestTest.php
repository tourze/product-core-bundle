<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Tourze\ProductCoreBundle\DTO\BatchUpdateProductStatusRequest;

/**
 * @internal
 */
#[CoversClass(BatchUpdateProductStatusRequest::class)]
final class BatchUpdateProductStatusRequestTest extends TestCase
{
    public function testDefaultConstruct(): void
    {
        $request = new BatchUpdateProductStatusRequest();

        $this->assertEmpty($request->productIds);
        $this->assertSame(0, $request->status);
    }

    public function testConstructWithParameters(): void
    {
        $productIds = [1, 2, 3];
        $status = 1;

        $request = new BatchUpdateProductStatusRequest($productIds, $status);

        $this->assertSame($productIds, $request->productIds);
        $this->assertSame($status, $request->status);
    }

    public function testConstructWithDuplicateIds(): void
    {
        $productIds = [1, 2, 2, 3, 1];
        $request = new BatchUpdateProductStatusRequest($productIds, 1);

        $this->assertSame([1, 2, 3], $request->productIds);
    }

    public function testGetUniqueProductIds(): void
    {
        $request = new BatchUpdateProductStatusRequest();
        $request->productIds = [1, 2, 2, 3, 1];

        $uniqueIds = $request->getUniqueProductIds();

        $this->assertSame([1, 2, 3], $uniqueIds);
    }

    public function testIsStatusValidWithValidStatuses(): void
    {
        $request1 = new BatchUpdateProductStatusRequest([], 0);
        $this->assertTrue($request1->isStatusValid());

        $request2 = new BatchUpdateProductStatusRequest([], 1);
        $this->assertTrue($request2->isStatusValid());
    }

    public function testIsStatusValidWithInvalidStatuses(): void
    {
        $request1 = new BatchUpdateProductStatusRequest([], -1);
        $this->assertFalse($request1->isStatusValid());

        $request2 = new BatchUpdateProductStatusRequest([], 2);
        $this->assertFalse($request2->isStatusValid());
    }

    public function testGetStatusDescription(): void
    {
        $request1 = new BatchUpdateProductStatusRequest([], 0);
        $this->assertSame('下架', $request1->getStatusDescription());

        $request2 = new BatchUpdateProductStatusRequest([], 1);
        $this->assertSame('上架', $request2->getStatusDescription());
    }

    public function testValidationWithValidData(): void
    {
        $request = new BatchUpdateProductStatusRequest([1, 2, 3], 1);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertCount(0, $violations);
    }

    public function testValidationWithEmptyProductIds(): void
    {
        $request = new BatchUpdateProductStatusRequest([], 1);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('商品ID列表不能为空', (string) $violations);
    }

    public function testValidationWithInvalidStatus(): void
    {
        $request = new BatchUpdateProductStatusRequest([1, 2], 2);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('状态值只能是0(下架)或1(上架)', (string) $violations);
    }

    public function testValidationWithTooManyProductIds(): void
    {
        $request = new BatchUpdateProductStatusRequest(range(1, 1001), 1);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('单次批量操作不能超过1000个商品', (string) $violations);
    }

    public function testValidationWithInvalidProductIdType(): void
    {
        $request = new BatchUpdateProductStatusRequest();
        $request->productIds = [1, 0, 3]; // 使用包含0的数组，0是无效的商品ID
        $request->status = 1;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
    }

    public function testValidationWithNegativeProductId(): void
    {
        $request = new BatchUpdateProductStatusRequest();
        $request->productIds = [-1, 2, 3];
        $request->status = 1;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('商品ID必须为正整数', (string) $violations);
    }

    public function testValidationWithZeroProductId(): void
    {
        $request = new BatchUpdateProductStatusRequest();
        $request->productIds = [0, 1, 2];
        $request->status = 1;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('商品ID必须为正整数', (string) $violations);
    }
}
