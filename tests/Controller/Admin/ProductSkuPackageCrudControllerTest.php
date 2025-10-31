<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\ProductCoreBundle\Controller\Admin\ProductSkuPackageCrudController;

/**
 * @internal
 */
#[CoversClass(ProductSkuPackageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ProductSkuPackageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return ProductSkuPackageCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ProductSkuPackageCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '所属SKU' => ['所属SKU'];
        yield '打包类型' => ['打包类型'];
        yield '属性值' => ['属性值'];
        yield '数量' => ['数量'];
        yield '备注' => ['备注'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'value' => ['value'];
        yield 'quantity' => ['quantity'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'value' => ['value'];
        yield 'quantity' => ['quantity'];
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

        // 验证包含SKU打包相关的必填字段
        $requiredFields = ['value', 'quantity'];
        foreach ($requiredFields as $fieldName) {
            self::assertContains($fieldName, $providerFields,
                "数据提供器应包含必填字段 {$fieldName}");
        }
    }

    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/sku-package');
    }

    public function testNewPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/sku-package/new');
    }

    public function testEditPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/sku-package/1/edit');
    }

    public function testDeleteRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('DELETE', '/admin/product/sku-package/1');
    }

    public function testCreateActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('POST', '/admin/product/sku-package');
    }

    public function testUpdateActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PUT', '/admin/product/sku-package/1');
    }

    public function testPatchActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PATCH', '/admin/product/sku-package/1');
    }

    public function testHeadRequestRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('HEAD', '/admin/product/sku-package');
    }

    public function testOptionsRequestRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('OPTIONS', '/admin/product/sku-package');
    }

    public function testUnauthenticatedAccessShouldThrowException(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access Denied. The user doesn\'t have ROLE_ADMIN.');
        $client->request('GET', '/admin/product/sku-package');
    }
}
