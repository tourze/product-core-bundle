<?php

namespace ProductCoreBundle\Event;

use Tourze\UserEventBundle\Event\UserInteractionEvent;

class StockWarningEvent extends UserInteractionEvent
{
    private string $skuId;

    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function setSkuId(string $skuId): void
    {
        $this->skuId = $skuId;
    }
}
