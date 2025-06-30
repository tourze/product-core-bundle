<?php

namespace Tourze\ProductCoreBundle\Service;

use Tourze\ProductCoreBundle\Entity\Category;
use Tourze\ProductCoreBundle\Repository\CategoryRepository;
use Tourze\Symfony\AopCacheBundle\Attribute\Cacheble;

class CategoryService
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    #[Cacheble(tags: [Category::class])]
    public function findSearchableId(array|string|int $ids): array
    {
        $categories = $this->categoryRepository->findBy(['id' => $ids]);
        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds = array_merge($categoryIds, $category->getSearchableId());
        }

        return array_values(array_unique($categoryIds));
    }
}
