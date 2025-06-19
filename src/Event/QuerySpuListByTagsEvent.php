<?php

namespace ProductBundle\Event;

// use DoctrineEnhanceBundle\Traits\QueryBuilderAware; // Trait not found
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\JsonRPCEndpointBundle\Traits\InterruptCallbackTrait;

class QuerySpuListByTagsEvent extends Event
{
    use InterruptCallbackTrait;
    // use QueryBuilderAware; // Trait not found

    private array $tags;

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}
