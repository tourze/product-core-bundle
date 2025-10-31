<?php

namespace Tourze\ProductCoreBundle\Event;

use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

final class SpuDetailEvent extends UserInteractionEvent
{
    /** @var array<mixed> */
    private array $result = [];

    private Spu $spu;

    /** @return array<mixed> */
    public function getResult(): array
    {
        return $this->result;
    }

    /** @param array<mixed> $result */
    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    public function getSpu(): Spu
    {
        return $this->spu;
    }

    public function setSpu(Spu $spu): void
    {
        $this->spu = $spu;
    }
}
