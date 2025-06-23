<?php

namespace ProductCoreBundle\Service;

use ProductCoreBundle\Entity\Tag;
use ProductCoreBundle\Repository\TagRepository;
use Tourze\Symfony\AopCacheBundle\Attribute\Cacheble;

class TagService
{
    public function __construct(private readonly TagRepository $tagRepository)
    {
    }

    #[Cacheble(ttl: 86400, tags: [Tag::class])]
    public function findTags(array $items): array
    {
        $postTags = [];
        foreach ($items as $item) {
            $tag = null;
            if (isset($item['id'])) {
                $tag = $this->tagRepository->find($item['id']);
            }

            if (isset($item['name'])) {
                $tag = $this->tagRepository->findOneBy(['name' => $item['name']]);
            }

            if ($tag !== null) {
                $postTags[$tag->getId()] = $tag->getId();
            }
        }

        return array_values($postTags);
    }
}
