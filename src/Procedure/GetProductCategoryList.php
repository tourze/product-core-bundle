<?php

namespace ProductBundle\Procedure;

use AppBundle\Service\UserTagService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use ProductBundle\Entity\Category;
use Symfony\Bundle\SecurityBundle\Security;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;

#[MethodTag('产品模块')]
#[MethodDoc('获取所有的商品分类')]
#[MethodExpose('GetProductCategoryList')]
class GetProductCategoryList extends CacheableProcedure
{
    #[MethodParam('上级目录，不传入的话则加载根目录数据')]
    public ?string $parent = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserTagService $userTagService,
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

        if ($parent) {
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
        if ($this->security->getUser()) {
            $tagIds = $this->userTagService->getTagIdsByUser($this->security->getUser());
            foreach ($tagIds as $k => $tagId) {
                $whereList[] = "JSON_SEARCH(a.showTags, 'one', :keyword_{$k}) IS NOT NULL";
                $qb->setParameter("keyword_{$k}", $tagId);
            }
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

    protected function getCacheKey(JsonRpcRequest $request): string
    {
        $key = static::buildParamCacheKey($request->getParams());
        if ($this->security->getUser()) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    protected function getCacheDuration(JsonRpcRequest $request): int
    {
        return MINUTE_IN_SECONDS;
    }

    protected function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield null;
    }
}
