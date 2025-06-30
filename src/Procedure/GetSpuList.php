<?php

namespace Tourze\ProductCoreBundle\Procedure;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Event\QuerySpuListByTagsEvent;
use Tourze\ProductCoreBundle\Service\CategoryService;
use Tourze\ProductCoreBundle\Service\TagService;

#[MethodTag(name: '产品模块')]
#[MethodDoc(summary: '获取SPU列表')]
#[MethodExpose(method: 'GetSpuList')]
class GetSpuList extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam(description: '查询的目录分类，如[1, 2, 3]')]
    public array $categoryIds = [];

    #[MethodParam(description: "SPU属性过滤条件 [ { name: '保质期', value: '180天' } ]")]
    public array $spuAttributes = [];

    #[MethodParam(description: '标签ID')]
    public array $tags = [];

    #[MethodParam(description: '扩展字段 (不要使用，计划废弃)')]
    public string $extend = '';

    #[MethodParam(description: '搜索关键词')]
    public string $keyword = '';

    #[MethodParam(description: '供应商信息')]
    public string $supplier = '';

    #[MethodParam(description: '商品类型')]
    public ?string $type = '';

    #[MethodParam(description: '物料编码')]
    public ?string $gtin = '';

    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly TagService $tagService,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $procedureLogger,
    ) {
    }

    public function execute(): array
    {
        $qb = $this->entityManager
            ->createQueryBuilder()
            ->from(Spu::class, 'a')
            ->select('a')
            ->andWhere('a.valid = true AND a.audited = true')
            ->addOrderBy('a.sortNumber', Criteria::DESC)
            ->addOrderBy('a.id', Criteria::DESC);

        // 查找指定目录
        if (!empty($this->categoryIds)) {
            $categoryIds = $this->categoryService->findSearchableId($this->categoryIds);
            if (empty($categoryIds)) {
                $qb->andWhere('3=2');
            } else {
                $qb->innerJoin('a.categories', 'c');
                $qb->andWhere('c.id IN (:categories)');
                $qb->setParameter('categories', $categoryIds);
            }
        }

        // 查找关键属性
        if (!empty($this->spuAttributes)) {
            $qb->innerJoin('a.attributes', 'attr');

            $attrParts = [];
            foreach ($this->spuAttributes as $i => $item) {
                $attrParts[] = "(attr.name = :spuAttributes_name_{$i} AND attr.value = :spuAttributes_value_{$i})";
                $qb->setParameter("spuAttributes_name_{$i}", $item['name']);
                $qb->setParameter("spuAttributes_value_{$i}", $item['value']);
            }

            $qb->andWhere(implode(' OR ', $attrParts));
        }

        // 关键词查找
        if (!empty($this->keyword)) {
            $keyword = addslashes($this->keyword);
            $keyword = htmlentities($keyword);
            $qb->andWhere('a.title LIKE :keyword1 OR a.gtin LIKE :keyword2');
            $qb->setParameter('keyword1', "%{$keyword}%");
            $qb->setParameter('keyword2', "%{$keyword}%");
        }

        if (!empty($this->type)) {
            $qb->andWhere('a.type = :type')->setParameter('type', $this->type);
        }

        if (!empty($this->gtin)) {
            $qb->andWhere('a.gtin LIKE :gtin');
            $qb->setParameter('gtin', "%{$this->gtin}%");
        }

        if (!empty($this->supplier)) {
            $supplier = $this->entityManager
                ->createQueryBuilder()
                // Supplier integration removed - AppBundle not available
                // ->from(Supplier::class, 'a')
                ->select('a')
                ->andWhere('(a.id = :id OR a.title = :title) AND a.valid = true')
                ->setParameter('id', $this->supplier)
                ->setParameter('title', strval($this->supplier))
                ->getQuery()
                ->getOneOrNullResult();

            if ($supplier !== null) {
                $qb->andWhere('a.supplier = :supplier');
                $qb->setParameter('supplier', $supplier);
            } else {
                // 不满足条件，就不显示所有数据了
                $qb->andWhere('a.id = 0');
            }
        }

        // 标签查找
        $event = new QuerySpuListByTagsEvent();
        $event->setQueryBuilder($qb);
        $event->setTags($this->tags);
        $this->eventDispatcher->dispatch($event);
        $this->procedureLogger->debug('最终参与筛选的标签列表', [
            'tags' => $event->getTags(),
        ]);
        if (!empty($event->getTags())) {
            $postTags = $this->tagService->findTags($event->getTags());
            if (!empty($postTags)) {
                $qb->innerJoin('a.tags', 't');
                $qb->andWhere('t.id IN (:tags)');
                $qb->setParameter('tags', $postTags);
            }
        }

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

        return $this->fetchList($qb, $this->formatItem(...));
    }

    private function formatItem(Spu $spu): array
    {
        $result = $spu->retrieveSpuArray();
        if (!empty($result['skus'])) {
            foreach ($result['skus'] as $k => $sku) {
                if (empty($sku['valid'])) {
                    unset($result['skus'][$k]);
                }
            }
            $result['skus'] = array_values($result['skus']);
        }

        return $result;
    }
}
