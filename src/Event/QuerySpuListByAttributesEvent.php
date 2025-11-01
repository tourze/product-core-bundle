<?php

namespace Tourze\ProductCoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tourze\JsonRPC\Core\Traits\InterruptCallbackTrait;

final class QuerySpuListByAttributesEvent extends Event
{
    use InterruptCallbackTrait;
    // use QueryBuilderAware; // Trait not found

    /** @var array<mixed> */
    private array $attributes;

    /** @return array<mixed> */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /** @param array<mixed> $attributes */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }
}
