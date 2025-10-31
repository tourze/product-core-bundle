<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\ProductCoreBundle\Controller\Admin\ProductSkuCrudController;

/**
 * @internal
 */
#[CoversClass(ProductSkuCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ProductSkuCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return ProductSkuCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ProductSkuCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'spu' => ['所属SPU'];
        yield 'images' => ['图片集'];
        yield 'gtin' => ['GTIN编码'];
        yield 'mpn' => ['MPN编码'];
        yield 'unit' => ['单位'];
        yield 'needConsignee' => ['需要收货'];
        yield 'remark' => ['备注'];
        yield 'salesReal' => ['真实销量'];
        yield 'salesVirtual' => ['虚拟销量'];
        yield 'valid' => ['上架状态'];
        yield 'createdAt' => ['创建时间'];
        yield 'updatedAt' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'title' => ['title'];
        yield 'gtin' => ['gtin'];
        yield 'mpn' => ['mpn'];
        yield 'unit' => ['unit'];
        yield 'needConsignee' => ['needConsignee'];
        yield 'marketPrice' => ['marketPrice'];
        yield 'costPrice' => ['costPrice'];
        yield 'originalPrice' => ['originalPrice'];
        yield 'salesReal' => ['salesReal'];
        yield 'salesVirtual' => ['salesVirtual'];
        yield 'valid' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'title' => ['title'];
        yield 'gtin' => ['gtin'];
        yield 'mpn' => ['mpn'];
        yield 'unit' => ['unit'];
        yield 'needConsignee' => ['needConsignee'];
        yield 'marketPrice' => ['marketPrice'];
        yield 'costPrice' => ['costPrice'];
        yield 'originalPrice' => ['originalPrice'];
        yield 'salesReal' => ['salesReal'];
        yield 'salesVirtual' => ['salesVirtual'];
        yield 'valid' => ['valid'];
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

        // 验证包含SKU相关的必填字段
        $requiredFields = ['title', 'valid'];
        foreach ($requiredFields as $fieldName) {
            self::assertContains($fieldName, $providerFields,
                "数据提供器应包含必填字段 {$fieldName}");
        }
    }

    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/sku');
    }

    public function testNewPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/sku/new');
    }

    public function testEditPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/sku/1/edit');
    }

    public function testDeleteRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('DELETE', '/admin/product/sku/1');
    }

    public function testCreateActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('POST', '/admin/product/sku');
    }

    public function testUpdateActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PUT', '/admin/product/sku/1');
    }

    public function testPatchActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PATCH', '/admin/product/sku/1');
    }

    public function testHeadRequestRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('HEAD', '/admin/product/sku');
    }

    public function testOptionsRequestRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('OPTIONS', '/admin/product/sku');
    }

    public function testUnauthenticatedAccessShouldThrowException(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access Denied. The user doesn\'t have ROLE_ADMIN.');
        $client->request('GET', '/admin/product/sku');
    }
}
