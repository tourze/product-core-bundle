<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\Exception\AdvertisementLimitException;
use Tourze\ProductCoreBundle\Service\AdvertisementChecker;

/**
 * @internal
 */
#[CoversClass(AdvertisementChecker::class)]
#[RunTestsInSeparateProcesses]
final class AdvertisementCheckerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testCanBeInstantiated(): void
    {
        $service = self::getService(AdvertisementChecker::class);
        $this->assertInstanceOf(AdvertisementChecker::class, $service);
    }

    public function testCheckLimitWords(): void
    {
        $checker = self::getService(AdvertisementChecker::class);

        // 测试正常内容，不应抛出异常
        $checker->checkLimitWords('这是一个正常的商品描述');

        // 测试包含极限词的内容，应抛出异常
        $this->expectException(AdvertisementLimitException::class);
        $this->expectExceptionMessage('您的输入带有极限词，请检查并确认：销量第一');
        $checker->checkLimitWords('这是销量第一的商品');
    }
}
