<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\ProductCoreBundle\Controller\Admin\ProductPriceCrudController;

/**
 * @internal
 */
#[CoversClass(ProductPriceCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ProductPriceCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return ProductPriceCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ProductPriceCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'sku' => ['规格'];
        yield 'priceType' => ['价格类型'];
        yield 'currency' => ['币种'];
        yield 'price' => ['价格'];
        yield 'taxRate' => ['税率(%)'];
        yield 'formula' => ['公式'];
        yield 'priority' => ['优先级'];
        yield 'effectTime' => ['生效时间'];
        yield 'expireTime' => ['过期时间'];
        yield 'refundable' => ['允许退款'];
        yield 'isDefault' => ['是否默认'];
        yield 'remark' => ['备注'];
        yield 'description' => ['描述'];
        yield 'createdAt' => ['创建时间'];
        yield 'updatedAt' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'currency' => ['currency'];
        yield 'price' => ['price'];
        yield 'minBuyQuantity' => ['minBuyQuantity'];
        yield 'effectTime' => ['effectTime'];
        yield 'expireTime' => ['expireTime'];
        yield 'isDefault' => ['isDefault'];
        yield 'remark' => ['remark'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'currency' => ['currency'];
        yield 'price' => ['price'];
        yield 'minBuyQuantity' => ['minBuyQuantity'];
        yield 'effectTime' => ['effectTime'];
        yield 'expireTime' => ['expireTime'];
        yield 'isDefault' => ['isDefault'];
        yield 'remark' => ['remark'];
    }

    public function testNewPageFieldsProviderHasValidData(): void
    {
        $controller = $this->getControllerService();
        $displayedFields = [];
        foreach ($controller->configureFields('new') as $field) {
            if (is_string($field)) {
                continue;
            }
            $dto = $field->getAsDto();
            if ($dto->isDisplayedOn('new')) {
                $displayedFields[] = $dto;
            }
        }

        self::assertGreaterThan(0, count($displayedFields));

        $providerFields = array_map(
            static fn (array $item): string => $item[0],
            iterator_to_array(self::provideNewPageFields())
        );
        self::assertNotEmpty($providerFields);

        // 验证包含价格相关的必填字段
        $requiredFields = ['price'];
        foreach ($requiredFields as $fieldName) {
            self::assertContains($fieldName, $providerFields,
                "数据提供器应包含必填字段 {$fieldName}");
        }
    }

    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/price');
    }

    public function testNewPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/price/new');
    }

    public function testEditPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/price/1/edit');
    }

    public function testDeleteRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('DELETE', '/admin/product/price/1');
    }

    public function testCreateActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('POST', '/admin/product/price');
    }

    public function testUpdateActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PUT', '/admin/product/price/1');
    }

    public function testPatchActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PATCH', '/admin/product/price/1');
    }

    public function testHeadRequestRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('HEAD', '/admin/product/price');
    }

    public function testOptionsRequestRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('OPTIONS', '/admin/product/price');
    }

    public function testUnauthenticatedAccessShouldThrowException(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access Denied. The user doesn\'t have ROLE_ADMIN.');
        $client->request('GET', '/admin/product/price');
    }
}
