<?php

namespace ProductCoreBundle\Event;

use ProductCoreBundle\Entity\Spu;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

class SpuDetailEvent extends UserInteractionEvent
{
    private array $result = [];
    private Spu $spu;

    public function getResult(): array
    {
        return $this->result;
    }

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
