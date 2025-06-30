<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\ProductCoreBundle\AdminMenu;

class AdminMenuTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $adminMenu = new AdminMenu($linkGenerator);
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testMenuBuilding(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')
            ->willReturn('/test-uri');

        $menuItem = $this->createMock(ItemInterface::class);
        $ecommerceItem = $this->createMock(ItemInterface::class);
        
        // 模拟第一次调用返回 null，后续调用返回 ecommerceItem
        $callCount = 0;
        $menuItem->method('getChild')
            ->willReturnCallback(function ($name) use ($ecommerceItem, &$callCount) {
                if ($name === '电商中心') {
                    $callCount++;
                    return $callCount === 1 ? null : $ecommerceItem;
                }
                return null;
            });
        
        $menuItem->method('addChild')
            ->willReturn($ecommerceItem);

        $ecommerceItem->method('addChild')
            ->willReturnSelf();
        
        $ecommerceItem->method('setUri')
            ->willReturnSelf();

        $adminMenu = new AdminMenu($linkGenerator);
        $adminMenu($menuItem);
        
        // 测试完成，无异常
        $this->assertTrue(true);
    }
}