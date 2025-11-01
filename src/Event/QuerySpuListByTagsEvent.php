<?php

namespace Tourze\ProductCoreBundle\Event;

use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\JsonRPC\Core\Traits\InterruptCallbackTrait;

final class QuerySpuListByTagsEvent extends Event
{
    use InterruptCallbackTrait;
    // use QueryBuilderAware; // Trait not found

    /** @var array<mixed> */
    private array $tags;

    private ?QueryBuilder $queryBuilder = null;

    /** @return array<mixed> */
    public function getTags(): array
    {
        return $this->tags;
    }

    /** @param array<mixed> $tags */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(?QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }
}
