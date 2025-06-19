<?php

namespace ProductBundle\Event;

// use DoctrineEnhanceBundle\Traits\QueryBuilderAware; // Trait not found
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\JsonRPCEndpointBundle\Traits\InterruptCallbackTrait;

class QuerySpuListByAttributesEvent extends Event
{
    use InterruptCallbackTrait;
    // use QueryBuilderAware; // Trait not found

    private array $attributes;

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }
}
