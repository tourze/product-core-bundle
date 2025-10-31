# Product Core Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/product-core-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/product-core-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/product-core-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/product-core-bundle)
[![License](https://img.shields.io/packagist/l/tourze/product-core-bundle.svg?style=flat-square)]
(https://github.com/tourze/php-monorepo/blob/master/LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/product-core-bundle.svg?style=flat-square)]
(https://scrutinizer-ci.com/g/tourze/product-core-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)]
(https://codecov.io/gh/tourze/php-monorepo)

一个综合性的 Symfony 包，用于管理产品核心数据，包括 SPU、SKU、分类、品牌、定价和库存管理。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [配置](#配置)
- [快速开始](#快速开始)
- [依赖要求](#依赖要求)
- [控制台命令](#控制台命令)
- [API 端点](#api-端点)
- [实体关系](#实体关系)
- [事件](#事件)
- [高级用法](#高级用法)
- [测试](#测试)
- [贡献](#贡献)
- [许可证](#许可证)
- [参考资料](#参考资料)

## 功能特性

- **完整的产品管理**：SPU/SKU 管理，包含属性、定价和库存功能
- **分类管理**：支持层级结构的产品分类和限购规则
- **品牌管理**：产品品牌组织和管理
- **价格管理**：灵活的定价系统，支持多种价格类型和条件
- **库存管理**：实时库存跟踪和详细的变更日志
- **限购规则**：可配置的分类、SPU 和 SKU 购买限制
- **运费模板**：运费计算模板
- **数据采集**：内置命令用于从外部数据源抓取产品数据
- **自动调度**：基于时间规则的 SPU 自动上架和下架
- **JSON-RPC API**：RESTful API 端点用于产品数据访问
- **管理界面**：基于 EasyAdmin 的管理界面

## 安装

```bash
composer require tourze/product-core-bundle
```

## 配置

将包添加到你的 `config/bundles.php` 中：

```php
return [
    // ...
    Tourze\ProductCoreBundle\ProductCoreBundle::class => ['all' => true],
];
```

## 快速开始

### 基本用法

```php
<?php

use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Category;
use Tourze\ProductCoreBundle\Entity\Brand;

// 创建产品分类
$category = new Category();
$category->setTitle('电子产品');
$category->setDescription('电子产品分类');

// 创建品牌
$brand = new Brand();
$brand->setName('苹果');
$brand->setLogoUrl('https://example.com/logo.png');

// 创建 SPU（标准产品单元）
$spu = new Spu();
$spu->setTitle('iPhone 15 Pro');
$spu->setCategory($category);
$spu->setBrand($brand);
$spu->setContent('搭载先进功能的最新款 iPhone');
$spu->setValid(true);

// 创建 SKU（库存单位）
$sku = new Sku();
$sku->setSpu($spu);
$sku->setGtin('1234567890123');
$sku->setUnit('个');
$sku->setValid(true);

// 添加到实体管理器
$entityManager->persist($category);
$entityManager->persist($brand);
$entityManager->persist($spu);
$entityManager->persist($sku);
$entityManager->flush();
```

### 使用服务

```php
<?php

use Tourze\ProductCoreBundle\Service\StockService;
use Tourze\ProductCoreBundle\Service\PriceService;
use Tourze\ProductCoreBundle\Service\CategoryService;

// 库存管理
$stockService = $container->get(StockService::class);
$stockService->updateStock($sku, 100, '初始库存');

// 价格管理
$priceService = $container->get(PriceService::class);
$price = $priceService->getValidPrice($sku, PriceType::SALE);

// 分类管理
$categoryService = $container->get(CategoryService::class);
$categories = $categoryService->getHierarchicalCategories();
```

## 依赖要求

此包需要：

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- Doctrine DBAL 4.0+

其他依赖：
- `moneyphp/money` - 用于货币计算
- `nesbot/carbon` - 用于日期/时间处理
- `knplabs/knp-menu` - 用于菜单生成
- `yiisoft/arrays` - 用于数组工具
- `yiisoft/json` - 用于 JSON 处理

## 控制台命令

包提供了几个用于产品管理的控制台命令：

### 自动管理命令

#### `product:auto-release-spu`
根据预定的发布时间自动上架 SPU。

```bash
php bin/console product:auto-release-spu
```

此命令每分钟通过 cron 运行，并：
- 查找 `autoReleaseTime` 在过去的 SPU
- 检查它们是否还没有超过 `autoTakeDownTime`
- 如果满足条件，将它们设置为有效（上架）

#### `product:auto-take-down-spu`
根据预定的下架时间自动下架 SPU。

```bash
php bin/console product:auto-take-down-spu
```

此命令每分钟通过 cron 运行，并：
- 查找有效的且 `autoTakeDownTime` 在过去的 SPU
- 将它们设置为无效（下架）

### 数据采集命令

#### `product:cib-mall:crawl-category`
从兴业银行商城抓取产品分类。

```bash
php bin/console product:cib-mall:crawl-category
```

此命令：
- 从兴业银行商城 API 获取分类数据
- 创建或更新分类实体
- 处理层级分类结构

#### `product:cib-mall:crawl-spu`
从兴业银行商城抓取 SPU 数据。

```bash
php bin/console product:cib-mall:crawl-spu
```

此命令：
- 从兴业银行商城 API 获取 SPU 数据
- 创建或更新 SPU、SKU 和相关实体
- 处理产品属性、定价和图片

## API 端点

包提供了 JSON-RPC API 端点：

- `GetProductCategoryList`：获取产品分类
- `ProductSkuDetail`：获取详细的 SKU 信息
- `ProductSpuDetail`：获取详细的 SPU 信息

## 实体关系

包包含以下主要实体：

- **Category**：带层级结构的产品分类
- **Brand**：产品品牌
- **Spu**：标准产品单元（产品型号）
- **Sku**：库存单位（具体产品变体）
- **Price**：SKU 的定价信息
- **Stock**：SKU 的库存水平
- **StockLog**：库存变更历史
- **SpuAttribute/SkuAttribute**：产品属性
- **FreightTemplate**：运费模板

## 事件

包为扩展性调度了几个事件：

- `QuerySpuListByAttributesEvent`：通过属性自定义 SPU 查询
- `QuerySpuListByTagsEvent`：通过标签自定义 SPU 查询
- `SpuDetailEvent`：修改 SPU 详情响应
- `StockWarningEvent`：处理低库存警告

## 高级用法

### 自定义价格计算

```php
use Tourze\ProductCoreBundle\Service\PriceService;
use Tourze\ProductCoreBundle\Event\PriceCalculationEvent;

// 自定义价格计算逻辑
$priceService = $container->get(PriceService::class);
$customPrice = $priceService->calculateCustomPrice($sku, $quantity, $userId);
```

### 库存管理

```php
use Tourze\ProductCoreBundle\Service\StockService;

// 高级库存操作
$stockService = $container->get(StockService::class);
$stockService->reserveStock($sku, $quantity, $orderId);
$stockService->releaseReservedStock($sku, $quantity, $orderId);
```

### 分类层级操作

```php
use Tourze\ProductCoreBundle\Service\CategoryService;

// 获取分类树
$categoryService = $container->get(CategoryService::class);
$categoryTree = $categoryService->buildCategoryTree();
$breadcrumbs = $categoryService->getCategoryBreadcrumbs($category);
```

## 测试

### 运行测试

由于当前完整测试套件存在 Symfony 缓存冲突，建议按类别运行测试：

```bash
# 单元测试（Entity、Enum、Event、Exception）
./vendor/bin/phpunit packages/product-core-bundle/tests/Entity/
./vendor/bin/phpunit packages/product-core-bundle/tests/Enum/
./vendor/bin/phpunit packages/product-core-bundle/tests/Event/
./vendor/bin/phpunit packages/product-core-bundle/tests/Exception/

# 集成测试（由于缓存冲突需要单独处理）
# Repository、Service、Controller 测试目前受到 Symfony 缓存冲突影响
```

### 测试状态

✅ **正常工作的测试**：
- Entity 测试（19 个测试）- 基本实体功能
- Enum 测试（18 个测试）- 枚举值测试
- Event 测试（4 个测试）- 事件调度和处理
- Exception 测试（3 个测试）- 自定义异常行为

⚠️ **已知问题**：
- 集成测试遇到 Symfony 缓存冲突
- Repository/Service/Controller 测试需要解决缓存问题
- 详见 [GitHub Issue #821](https://github.com/tourze/php-monorepo/issues/821) 跟踪进展

### 代码质量

运行静态分析：

```bash
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/product-core-bundle
```

注意：核心实体（Sku、Spu）存在一些复杂度警告，已在 [GitHub Issue #822](https://github.com/tourze/php-monorepo/issues/822) 中跟踪。

## 贡献

1. Fork 仓库
2. 创建功能分支
3. 进行更改
4. 为更改添加测试
5. 运行测试套件
6. 提交拉取请求

## 许可证

此包基于 MIT 许可证发布。查看 [LICENSE](LICENSE) 文件了解详情。

## 参考资料

- [美团快驴产品发布规范][meituan-link]
- [天猫达尔文平台][tmall-link]
- [Shopware 开发者文档](https://developer.shopware.com/docs/concepts/commerce/catalog/sales-channels)
- [电商平台产品管理策略](https://www.woshipm.com/operate/4707858.html)

[meituan-link]: https://rules-center.meituan.com/m/detail/guize/264?activeRule=1&commonType=17
[tmall-link]: https://developer.alibaba.com/docs/doc.htm?spm=a219a.7629140.0.0.4d9775feZoWKMw&treeId=1&articleId=108954&docType=1