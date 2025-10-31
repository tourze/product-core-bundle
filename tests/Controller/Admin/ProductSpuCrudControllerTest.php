<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\ProductCoreBundle\Controller\Admin\ProductSpuCrudController;

/**
 * @internal
 */
#[CoversClass(ProductSpuCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ProductSpuCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return ProductSpuCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ProductSpuCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield 'GTIN编码' => ['GTIN编码'];
        yield '主图' => ['主图'];
        yield '标题' => ['标题'];
        yield '类型' => ['类型'];
        yield '状态' => ['状态'];
        yield '上架状态' => ['上架状态'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'gtin' => ['gtin'];
        yield 'title' => ['title'];
        yield 'type' => ['type'];
        yield 'subtitle' => ['subtitle'];
        yield 'valid' => ['valid'];
        yield 'sortNumber' => ['sortNumber'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'gtin' => ['gtin'];
        yield 'title' => ['title'];
        yield 'type' => ['type'];
        yield 'subtitle' => ['subtitle'];
        yield 'valid' => ['valid'];
        yield 'sortNumber' => ['sortNumber'];
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

        // 验证包含SPU相关的必填字段
        $requiredFields = ['title', 'type'];
        foreach ($requiredFields as $fieldName) {
            self::assertContains($fieldName, $providerFields,
                "数据提供器应包含必填字段 {$fieldName}");
        }
    }

    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/spu');
    }

    public function testNewPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/spu/new');
    }

    public function testEditPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/product/spu/1/edit');
    }

    public function testDeleteRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('DELETE', '/admin/product/spu/1');
    }

    public function testCreateActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('POST', '/admin/product/spu');
    }

    public function testUpdateActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PUT', '/admin/product/spu/1');
    }

    public function testPatchActionRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PATCH', '/admin/product/spu/1');
    }

    public function testHeadRequestRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('HEAD', '/admin/product/spu');
    }

    public function testOptionsRequestRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('OPTIONS', '/admin/product/spu');
    }

    public function testUnauthenticatedAccessShouldThrowException(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access Denied. The user doesn\'t have ROLE_ADMIN.');
        $client->request('GET', '/admin/product/spu');
    }
}
