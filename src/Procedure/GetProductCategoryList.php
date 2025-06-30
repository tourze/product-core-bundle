<?php

namespace Tourze\ProductCoreBundle\Procedure;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;
use Tourze\ProductCoreBundle\Entity\Category;

#[MethodTag(name: '产品模块')]
#[MethodDoc(summary: '获取所有的商品分类')]
#[MethodExpose(method: 'GetProductCategoryList')]
class GetProductCategoryList extends CacheableProcedure
{
    #[MethodParam(description: '上级目录，不传入的话则加载根目录数据')]
    public ?string $parent = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $parent = null;
        if (null !== $this->parent) {
            $parent = $this->entityManager
                ->createQueryBuilder()
                ->from(Category::class, 'a')
                ->select('a')
                ->andWhere('a.id = :id AND a.valid = true')
                ->setParameter('id', $this->parent)
                ->getQuery()
                ->getOneOrNullResult();
            if ($parent === null) {
                throw new ApiException('找不到父分类');
            }
        }

        $qb = $this->entityManager
            ->createQueryBuilder()
            ->from(Category::class, 'a')
            ->select('a')
            ->addOrderBy('a.sortNumber', Criteria::DESC)
            ->andWhere('a.valid = :valid')
            ->setParameter('valid', true);

        if ($parent !== null) {
            $qb->andWhere('a.parent = :parent')
                ->setParameter('parent', $parent);
        } else {
            $qb->andWhere('a.parent IS NULL');
        }

        // 额外的标签查询
        $whereList = [
            'a.showTags is null',
            'JSON_LENGTH(a.showTags) = 0',
        ];
        if ($this->security->getUser() !== null) {
            // UserTagService integration removed - AppBundle not available
            // Tag-based filtering disabled
        }
        $whereList = implode(' OR ', $whereList);
        $qb->andWhere($whereList);

        $topList = $qb
            ->getQuery()
            ->getResult();

        $result = [
            'list' => [],
        ];
        foreach ($topList as $category) {
            /* @var Category $category */
            $result['list'][] = $category->getSimpleArray();
        }

        return $result;
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $key = static::buildParamCacheKey($request->getParams());
        if ($this->security->getUser() !== null) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60; // 1 minute
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield null;
    }
}
