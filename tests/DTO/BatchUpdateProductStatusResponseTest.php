<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\BatchUpdateProductStatusResponse;

/**
 * @internal
 */
#[CoversClass(BatchUpdateProductStatusResponse::class)]
final class BatchUpdateProductStatusResponseTest extends TestCase
{
    public function testConstruct(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 5,
            failedCount: 2,
            failedIds: [100, 101],
            totalCount: 7,
            status: 1
        );

        $this->assertSame(5, $response->successCount);
        $this->assertSame(2, $response->failedCount);
        $this->assertSame([100, 101], $response->failedIds);
        $this->assertSame(7, $response->totalCount);
        $this->assertSame(1, $response->status);
    }

    public function testToArray(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 3,
            failedCount: 1,
            failedIds: [999],
            totalCount: 4,
            status: 0
        );

        $array = $response->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('successCount', $array);
        $this->assertArrayHasKey('failedCount', $array);
        $this->assertArrayHasKey('failedIds', $array);
        $this->assertArrayHasKey('totalCount', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('statusDescription', $array);
        $this->assertArrayHasKey('isSuccess', $array);
        $this->assertArrayHasKey('successRate', $array);

        $this->assertSame(3, $array['successCount']);
        $this->assertSame(1, $array['failedCount']);
        $this->assertSame([999], $array['failedIds']);
        $this->assertSame(4, $array['totalCount']);
        $this->assertSame(0, $array['status']);
        $this->assertSame('下架', $array['statusDescription']);
        $this->assertFalse($array['isSuccess']);
        $this->assertSame(75.0, $array['successRate']);
    }

    public function testIsSuccessWhenNoFailures(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 5,
            failedCount: 0,
            failedIds: [],
            totalCount: 5,
            status: 1
        );

        $this->assertTrue($response->isSuccess());
    }

    public function testIsSuccessWhenHasFailures(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 3,
            failedCount: 2,
            failedIds: [100, 101],
            totalCount: 5,
            status: 1
        );

        $this->assertFalse($response->isSuccess());
    }

    public function testGetSuccessRateWithZeroTotal(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 0,
            failedCount: 0,
            failedIds: [],
            totalCount: 0,
            status: 1
        );

        $this->assertSame(0.0, $response->getSuccessRate());
    }

    public function testGetSuccessRateWithPartialSuccess(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 3,
            failedCount: 1,
            failedIds: [100],
            totalCount: 4,
            status: 1
        );

        $this->assertSame(75.0, $response->getSuccessRate());
    }

    public function testGetSuccessRateWithPerfectSuccess(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 10,
            failedCount: 0,
            failedIds: [],
            totalCount: 10,
            status: 1
        );

        $this->assertSame(100.0, $response->getSuccessRate());
    }

    public function testGetSuccessRateWithCompleteFailure(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 0,
            failedCount: 5,
            failedIds: [1, 2, 3, 4, 5],
            totalCount: 5,
            status: 1
        );

        $this->assertSame(0.0, $response->getSuccessRate());
    }

    public function testGetStatusDescriptionForUpgrade(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 1,
            failedCount: 0,
            failedIds: [],
            totalCount: 1,
            status: 1
        );

        $this->assertSame('上架', $response->getStatusDescription());
    }

    public function testGetStatusDescriptionForDowngrade(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 1,
            failedCount: 0,
            failedIds: [],
            totalCount: 1,
            status: 0
        );

        $this->assertSame('下架', $response->getStatusDescription());
    }

    public function testHasFailuresWhenNoFailures(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 3,
            failedCount: 0,
            failedIds: [],
            totalCount: 3,
            status: 1
        );

        $this->assertFalse($response->hasFailures());
    }

    public function testHasFailuresWhenHasFailures(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 2,
            failedCount: 1,
            failedIds: [100],
            totalCount: 3,
            status: 1
        );

        $this->assertTrue($response->hasFailures());
    }

    public function testGetSummaryForUpgradeOperation(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 8,
            failedCount: 2,
            failedIds: [100, 101],
            totalCount: 10,
            status: 1
        );

        $summary = $response->getSummary();
        $expected = '批量上架操作完成: 总计10个，成功8个，失败2个，成功率80.00%';

        $this->assertSame($expected, $summary);
    }

    public function testGetSummaryForDowngradeOperation(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 5,
            failedCount: 0,
            failedIds: [],
            totalCount: 5,
            status: 0
        );

        $summary = $response->getSummary();
        $expected = '批量下架操作完成: 总计5个，成功5个，失败0个，成功率100.00%';

        $this->assertSame($expected, $summary);
    }

    public function testGetSummaryWithZeroTotal(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 0,
            failedCount: 0,
            failedIds: [],
            totalCount: 0,
            status: 1
        );

        $summary = $response->getSummary();
        $expected = '批量上架操作完成: 总计0个，成功0个，失败0个，成功率0.00%';

        $this->assertSame($expected, $summary);
    }

    public function testSuccessRateRounding(): void
    {
        $response = new BatchUpdateProductStatusResponse(
            successCount: 1,
            failedCount: 2,
            failedIds: [100, 101],
            totalCount: 3,
            status: 1
        );

        $this->assertSame(33.33, $response->getSuccessRate());
    }
}
