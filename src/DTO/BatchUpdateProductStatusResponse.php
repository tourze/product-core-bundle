<?php

namespace Tourze\ProductCoreBundle\DTO;

final class BatchUpdateProductStatusResponse
{
    /**
     * @param array<int> $failedIds
     */
    public function __construct(
        public readonly int $successCount,
        public readonly int $failedCount,
        public readonly array $failedIds,
        public readonly int $totalCount,
        public readonly int $status,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'successCount' => $this->successCount,
            'failedCount' => $this->failedCount,
            'failedIds' => $this->failedIds,
            'totalCount' => $this->totalCount,
            'status' => $this->status,
            'statusDescription' => $this->getStatusDescription(),
            'isSuccess' => $this->isSuccess(),
            'successRate' => $this->getSuccessRate(),
        ];
    }

    public function isSuccess(): bool
    {
        return 0 === $this->failedCount;
    }

    public function getSuccessRate(): float
    {
        if (0 === $this->totalCount) {
            return 0.0;
        }

        return round(($this->successCount / $this->totalCount) * 100, 2);
    }

    public function getStatusDescription(): string
    {
        return 1 === $this->status ? '上架' : '下架';
    }

    public function hasFailures(): bool
    {
        return $this->failedCount > 0;
    }

    public function getSummary(): string
    {
        $operation = $this->getStatusDescription();

        return sprintf(
            '批量%s操作完成: 总计%d个，成功%d个，失败%d个，成功率%.2f%%',
            $operation,
            $this->totalCount,
            $this->successCount,
            $this->failedCount,
            $this->getSuccessRate()
        );
    }
}
