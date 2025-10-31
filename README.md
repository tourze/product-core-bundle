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

A comprehensive Symfony bundle for managing product core data including SPU,  
SKU, categories, brands, pricing, and inventory management.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [Dependencies](#dependencies)
- [Console Commands](#console-commands)
- [API Endpoints](#api-endpoints)
- [Entity Relationships](#entity-relationships)
- [Events](#events)
- [Advanced Usage](#advanced-usage)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)
- [References](#references)

## Features

- **Complete Product Management**: SPU/SKU management with attributes, pricing, and inventory
- **Category Management**: Hierarchical product categories with limit rules
- **Brand Management**: Product brand organization and management
- **Price Management**: Flexible pricing system with multiple price types and conditions
- **Stock Management**: Real-time inventory tracking with detailed logging
- **Limit Rules**: Configurable purchase limits for categories, SPUs, and SKUs
- **Freight Templates**: Shipping cost calculation templates
- **Data Crawling**: Built-in commands for crawling product data from external sources
- **Auto Scheduling**: Automated SPU release and take-down based on time rules
- **JSON-RPC API**: RESTful API endpoints for product data access
- **Admin Interface**: EasyAdmin-based administration interface

## Installation

```bash
composer require tourze/product-core-bundle
```

## Configuration

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    Tourze\ProductCoreBundle\ProductCoreBundle::class => ['all' => true],
];
```

## Quick Start

### Basic Usage

```php
<?php

use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Category;
use Tourze\ProductCoreBundle\Entity\Brand;

// Create a product category
$category = new Category();
$category->setTitle('Electronics');
$category->setDescription('Electronic products');

// Create a brand
$brand = new Brand();
$brand->setName('Apple');
$brand->setLogoUrl('https://example.com/logo.png');

// Create an SPU (Standard Product Unit)
$spu = new Spu();
$spu->setTitle('iPhone 15 Pro');
$spu->setCategory($category);
$spu->setBrand($brand);
$spu->setContent('Latest iPhone with advanced features');
$spu->setValid(true);

// Create an SKU (Stock Keeping Unit)
$sku = new Sku();
$sku->setSpu($spu);
$sku->setGtin('1234567890123');
$sku->setUnit('piece');
$sku->setValid(true);

// Add to entity manager
$entityManager->persist($category);
$entityManager->persist($brand);
$entityManager->persist($spu);
$entityManager->persist($sku);
$entityManager->flush();
```

### Using Services

```php
<?php

use Tourze\ProductCoreBundle\Service\StockService;
use Tourze\ProductCoreBundle\Service\PriceService;
use Tourze\ProductCoreBundle\Service\CategoryService;

// Stock management
$stockService = $container->get(StockService::class);
$stockService->updateStock($sku, 100, 'Initial stock');

// Price management
$priceService = $container->get(PriceService::class);
$price = $priceService->getValidPrice($sku, PriceType::SALE);

// Category management
$categoryService = $container->get(CategoryService::class);
$categories = $categoryService->getHierarchicalCategories();
```

## Dependencies

This bundle requires:

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- Doctrine DBAL 4.0+

Additional dependencies:
- `moneyphp/money` - For monetary calculations
- `nesbot/carbon` - For date/time handling
- `knplabs/knp-menu` - For menu generation
- `yiisoft/arrays` - For array utilities
- `yiisoft/json` - For JSON handling

## Console Commands

The bundle provides several console commands for product management:

### Auto Management Commands

#### `product:auto-release-spu`
Automatically releases SPUs based on their scheduled release time.

```bash
php bin/console product:auto-release-spu
```

This command runs every minute via cron and:
- Finds SPUs with `autoReleaseTime` in the past
- Checks if they haven't passed their `autoTakeDownTime`
- Sets them as valid (released) if conditions are met

#### `product:auto-take-down-spu`
Automatically takes down SPUs based on their scheduled take-down time.

```bash
php bin/console product:auto-take-down-spu
```

This command runs every minute via cron and:
- Finds valid SPUs with `autoTakeDownTime` in the past
- Sets them as invalid (taken down)

### Data Crawling Commands

#### `product:cib-mall:crawl-category`
Crawls product categories from CIB Mall (China Industrial Bank Mall).

```bash
php bin/console product:cib-mall:crawl-category
```

This command:
- Fetches category data from CIB Mall API
- Creates or updates category entities
- Handles hierarchical category structures

#### `product:cib-mall:crawl-spu`
Crawls SPU data from CIB Mall.

```bash
php bin/console product:cib-mall:crawl-spu
```

This command:
- Fetches SPU data from CIB Mall API
- Creates or updates SPU, SKU, and related entities
- Handles product attributes, pricing, and images

## API Endpoints

The bundle provides JSON-RPC API endpoints:

- `GetProductCategoryList`: Retrieve product categories
- `ProductSkuDetail`: Get detailed SKU information
- `ProductSpuDetail`: Get detailed SPU information

## Entity Relationships

The bundle includes the following main entities:

- **Category**: Product categories with hierarchical structure
- **Brand**: Product brands
- **Spu**: Standard Product Units (product models)
- **Sku**: Stock Keeping Units (specific product variants)
- **Price**: Pricing information for SKUs
- **Stock**: Inventory levels for SKUs
- **StockLog**: Inventory change history
- **SpuAttribute/SkuAttribute**: Product attributes
- **FreightTemplate**: Shipping cost templates

## Events

The bundle dispatches several events for extensibility:

- `QuerySpuListByAttributesEvent`: Customize SPU queries by attributes
- `QuerySpuListByTagsEvent`: Customize SPU queries by tags
- `SpuDetailEvent`: Modify SPU detail responses
- `StockWarningEvent`: Handle low stock warnings

## Advanced Usage

### Custom Price Calculation

```php
use Tourze\ProductCoreBundle\Service\PriceService;
use Tourze\ProductCoreBundle\Event\PriceCalculationEvent;

// Custom price calculation logic
$priceService = $container->get(PriceService::class);
$customPrice = $priceService->calculateCustomPrice($sku, $quantity, $userId);
```

### Inventory Management

```php
use Tourze\ProductCoreBundle\Service\StockService;

// Advanced stock operations
$stockService = $container->get(StockService::class);
$stockService->reserveStock($sku, $quantity, $orderId);
$stockService->releaseReservedStock($sku, $quantity, $orderId);
```

### Category Hierarchy Operations

```php
use Tourze\ProductCoreBundle\Service\CategoryService;

// Get category tree
$categoryService = $container->get(CategoryService::class);
$categoryTree = $categoryService->buildCategoryTree();
$breadcrumbs = $categoryService->getCategoryBreadcrumbs($category);
```

## Testing

### Running Tests

Due to current Symfony cache conflicts in the full test suite, it's recommended to run tests by category:

```bash
# Unit tests (Entity, Enum, Event, Exception)
./vendor/bin/phpunit packages/product-core-bundle/tests/Entity/
./vendor/bin/phpunit packages/product-core-bundle/tests/Enum/
./vendor/bin/phpunit packages/product-core-bundle/tests/Event/
./vendor/bin/phpunit packages/product-core-bundle/tests/Exception/

# Integration tests (require individual attention due to cache conflicts)
# Repository, Service, Controller tests currently affected by Symfony cache conflicts
```

### Test Status

✅ **Working Tests**:
- Entity tests (19 tests) - Basic entity functionality
- Enum tests (18 tests) - Enumeration value tests  
- Event tests (4 tests) - Event dispatch and handling
- Exception tests (3 tests) - Custom exception behavior

⚠️ **Known Issues**:
- Integration tests experience Symfony cache conflicts
- Repository/Service/Controller tests require cache resolution
- See [GitHub Issue #821](https://github.com/tourze/php-monorepo/issues/821) for tracking

### Code Quality

Run static analysis:

```bash
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/product-core-bundle
```

Note: Some complexity warnings exist for core entities (Sku, Spu) which are tracked in [GitHub Issue #822](https://github.com/tourze/php-monorepo/issues/822).

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for your changes
5. Run the test suite
6. Submit a pull request

## License

This bundle is released under the MIT License. See the [LICENSE](LICENSE) file for details.

## References

- [Meituan Kuailu Product Publishing Standards][meituan-link]
- [Tmall Darwin Platform][tmall-link]
- [Shopware Developer Documentation](https://developer.shopware.com/docs/concepts/commerce/catalog/sales-channels)
- [E-commerce Platform Product Management Strategy](https://www.woshipm.com/operate/4707858.html)

[meituan-link]: https://rules-center.meituan.com/m/detail/guize/264?activeRule=1&commonType=17
[tmall-link]: https://developer.alibaba.com/docs/doc.htm?spm=a219a.7629140.0.0.4d9775feZoWKMw&treeId=1&articleId=108954&docType=1