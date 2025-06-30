<?php

namespace Tourze\ProductCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\DoctrineEntityLockBundle\Service\EntityLockService;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\StockLog;
use Tourze\ProductCoreBundle\Enum\StockChange;
use Tourze\ProductCoreBundle\Exception\StockOverloadException;

/**
 * 库存服务
 *
 * @see https://www.woshipm.com/pd/615772.html
 */
#[Autoconfigure(public: true)]
class StockService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EntityLockService $entityLockService,
    ) {
    }

    /**
     * 在一个事务内批量处理库存
     */
    public function batchProcess(array $logs): void
    {
        $this->entityManager->wrapInTransaction(function () use ($logs) {
            foreach ($logs as $log) {
                $this->process($log);
            }
        });
    }

    /**
     * 根据日志，处理库存变动
     */
    public function process(StockLog $log): void
    {
        if (isset($_ENV['FIXED_PRODUCT_STOCK_NUMBER'])) {
            return;
        }

        if (empty($log->getSkuName()) && $log->getSku() !== null) {
            $log->setSkuName((string) $log->getSku());
        }

        $sku = $log->getSku();
        $this->entityLockService->lockEntity($sku, function () use ($log, $sku) {
            // 增加：自有仓库通过采购入库，协同仓通过代销采购协议入库。
            if (StockChange::PUT === $log->getType()) {
                $sku->setValidStock($sku->getValidStock() + $log->getQuantity());
                $this->entityManager->persist($sku);

                $log->setValidStock($sku->getValidStock());
                $log->setLockStock($sku->getLockStock());
                $log->setSoldStock($sku->getSoldStock());
                $this->entityManager->persist($log);
            }

            // 锁定：下单之后锁定库存
            if (StockChange::LOCK === $log->getType()) {
                if ($sku->getValidStock() < $log->getQuantity()) {
                    throw new StockOverloadException('库存不足');
                }

                $sku->setValidStock($sku->getValidStock() - $log->getQuantity());
                $sku->setLockStock($sku->getLockStock() + $log->getQuantity());
                $this->entityManager->persist($sku);

                $log->setValidStock($sku->getValidStock());
                $log->setLockStock($sku->getLockStock());
                $log->setSoldStock($sku->getSoldStock());
                $this->entityManager->persist($log);
            }

            // 解锁：订单取消之后释放锁定库存。
            if (StockChange::UNLOCK === $log->getType()) {
                $sku->setValidStock($sku->getValidStock() + $log->getQuantity());
                $sku->setLockStock($sku->getLockStock() - $log->getQuantity());
                if ($sku->getLockStock() < 0) {
                    $sku->setLockStock(0);
                }

                $this->entityManager->persist($sku);

                $log->setValidStock($sku->getValidStock());
                $log->setLockStock($sku->getLockStock());
                $log->setSoldStock($sku->getSoldStock());
                $this->entityManager->persist($log);
            }

            // 扣减：支付成功之后扣减库存，扣减锁定库存。
            if (StockChange::DEDUCT === $log->getType()) {
                if ($sku->getValidStock() < $log->getQuantity()) {
                    throw new StockOverloadException('库存不足');
                }

                $sku->setValidStock($sku->getValidStock() - $log->getQuantity());
                $sku->setLockStock($sku->getLockStock() - $log->getQuantity());
                if ($sku->getLockStock() < 0) {
                    $sku->setLockStock(0);
                }

                $sku->setSoldStock($sku->getSoldStock() + $log->getQuantity());
                $this->entityManager->persist($sku);

                $log->setValidStock($sku->getValidStock());
                $log->setLockStock($sku->getLockStock());
                $log->setSoldStock($sku->getSoldStock());
                $this->entityManager->persist($log);
            }

            // 返还：退货/换货后返还库存。相当于增加库存。
            if (StockChange::RETURN === $log->getType()) {
                $sku->setValidStock($sku->getValidStock() + $log->getQuantity());
                $sku->setSoldStock($sku->getSoldStock() - $log->getQuantity());
                if ($sku->getSoldStock() < 0) {
                    $sku->setSoldStock(0);
                }

                $this->entityManager->persist($sku);

                $log->setValidStock($sku->getValidStock());
                $log->setLockStock($sku->getLockStock());
                $log->setSoldStock($sku->getSoldStock());
                $this->entityManager->persist($log);
            }

            $this->entityManager->flush();
        });
    }

    /**
     * 读取指定SKU的有效库存
     */
    public function getValidStock(Sku $sku): int
    {
        if (isset($_ENV['FIXED_PRODUCT_STOCK_NUMBER'])) {
            return intval($_ENV['FIXED_PRODUCT_STOCK_NUMBER']);
        }

        return $sku->getValidStock();
    }
}
