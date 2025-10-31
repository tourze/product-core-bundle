<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use Tourze\ProductCoreBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private ItemInterface $item;

    public function testServiceIsAccessible(): void
    {
        $service = self::getService(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $service);
    }

    public function testMenuBuilding(): void
    {
        // 测试__invoke方法可以被调用，不会抛出异常
        $this->expectNotToPerformAssertions();

        try {
            ($this->adminMenu)($this->item);
        } catch (\Throwable $e) {
            self::fail('AdminMenu __invoke method should not throw exception: ' . $e->getMessage());
        }
    }

    public function testMenuStructure(): void
    {
        // 为测试创建新的 mock 对象
        $mainItem = $this->createMock(ItemInterface::class);
        $childItem = $this->createMock(ItemInterface::class);

        $childItem->expects($this->exactly(7))
            ->method('addChild')
            ->willReturnCallback(function ($name) use ($childItem) {
                $expectedNames = [
                    '产品管理/SPU',
                    '商品规格/SKU',
                    '分类管理',
                    '品牌管理',
                    '运费模板',
                    '价格管理',
                    'SKU打包',
                ];
                $this->assertContains($name, $expectedNames);

                return $childItem;
            })
        ;

        $childItem->method('setUri')->willReturn($childItem);

        $mainItem->expects($this->once())
            ->method('getChild')
            ->with('电商中心')
            ->willReturn($childItem)
        ;

        ($this->adminMenu)($mainItem);
    }

    protected function onSetUp(): void
    {
        $this->item = $this->createMock(ItemInterface::class);

        // 设置 mock 的返回值以避免 null 引用
        $childItem = $this->createMock(ItemInterface::class);
        $this->item->method('addChild')->willReturn($childItem);

        // 使用 willReturnCallback 来模拟 getChild 的行为
        $this->item->method('getChild')->willReturnCallback(function ($name) use ($childItem) {
            return '电商中心' === $name ? $childItem : null;
        });

        // 设置子项目的 mock 方法
        $childItem->method('addChild')->willReturn($childItem);
        $childItem->method('setUri')->willReturn($childItem);
        $childItem->method('setAttribute')->willReturn($childItem);

        // 从容器中获取服务实例
        $this->adminMenu = self::getService(AdminMenu::class);
    }
}
