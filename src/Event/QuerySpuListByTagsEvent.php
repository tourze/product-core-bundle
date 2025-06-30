<?php

namespace Tourze\ProductCoreBundle\Event;

// use DoctrineEnhanceBundle\Traits\QueryBuilderAware; // Trait not found
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\JsonRPCEndpointBundle\Traits\InterruptCallbackTrait;

class QuerySpuListByTagsEvent extends Event
{
    use InterruptCallbackTrait;
    // use QueryBuilderAware; // Trait not found

    private array $tags;
    private ?\Doctrine\ORM\QueryBuilder $queryBuilder = null;

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getQueryBuilder(): ?\Doctrine\ORM\QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(?\Doctrine\ORM\QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }
}
