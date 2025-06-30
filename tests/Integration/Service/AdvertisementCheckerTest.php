<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Service\AdvertisementChecker;

class AdvertisementCheckerTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(AdvertisementChecker::class));
    }
}