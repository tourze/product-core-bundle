<?php

namespace Tourze\ProductCoreBundle\Procedure;

use Doctrine\ORM\QueryBuilder;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Repository\SpuRepository;
use Tourze\ProductCoreBundle\Service\ProductArrayFormatterService;

#[MethodTag(name: '产品模块')]
#[MethodDoc(summary: '获取商品列表（支持筛选）')]
#[MethodExpose(method: 'GetProductListWithFilter')]
final class GetProductListWithFilter extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam(description: '最低价格')]
    public ?float $minPrice = null;

    #[MethodParam(description: '关键词')]
    public ?string $keyword = null;

    #[MethodParam(description: '最高价格')]
    public ?float $maxPrice = null;

    #[MethodParam(description: '销量排序 (sales_desc, sales_asc)')]
    public ?string $salesSort = null;

    #[MethodParam(description: '价格排序 (price_desc, price_asc)')]
    public ?string $priceSort = null;

    #[MethodParam(description: '分类ID')]
    public ?int $categoryId = null;

    #[MethodParam(description: '标签ID')]
    public ?int $tagId = null;

    #[MethodParam(description: '是否包含SKU数据')]
    public bool $includeSku = true;

    public function __construct(
        private readonly SpuRepository $spuRepository,
        private readonly ProductArrayFormatterService $formatterService,
    ) {
    }

    public function execute(): array
    {
        $qb = $this->createBaseQueryBuilder();

        if (null !== $this->keyword && '' !== $this->keyword) {
            $keyword = trim($this->keyword);
            $qb->andWhere('s.title LIKE :keyword')
                ->setParameter('keyword', "%{$keyword}%")
            ;
        }

        $this->applyPriceFilter($qb);
        $this->applyCategoryFilter($qb);
        $this->applyTagFilter($qb);
        $this->applySorting($qb);

        // 如果有排序，使用手动分页避免 Doctrine 分页器的限制
        if ($this->needsSorting()) {
            return $this->executeWithManualPagination($qb);
        }

        return $this->fetchList($qb, $this->formatItem(...));
    }

    private function createBaseQueryBuilder(): QueryBuilder
    {
        $qb = $this->spuRepository
            ->createQueryBuilder('s')
            ->andWhere('s.valid = true')
        ;

        // 如果需要包含 SKU 数据且没有排序，预加载相关数据以避免 N+1 查询
        // 当有排序时，不能使用 fetch join，因为会和聚合函数冲突
        if ($this->includeSku && !$this->needsSorting()) {
            $qb->leftJoin('s.skus', 'sk')
                ->leftJoin('sk.attributes', 'sa')
                ->addSelect('sk', 'sa')
            ;
        }

        return $qb;
    }

    private function applyPriceFilter(QueryBuilder $qb): void
    {
        if (null === $this->minPrice && null === $this->maxPrice) {
            return;
        }

        $qb->innerJoin('s.skus', 'sku');

        if (null !== $this->minPrice) {
            $qb->andWhere('sku.marketPrice >= :minPrice')
                ->setParameter('minPrice', $this->minPrice)
            ;
        }

        if (null !== $this->maxPrice) {
            $qb->andWhere('sku.marketPrice <= :maxPrice')
                ->setParameter('maxPrice', $this->maxPrice)
            ;
        }
    }

    private function applyCategoryFilter(QueryBuilder $qb): void
    {
        if (null === $this->categoryId) {
            return;
        }

        $qb->innerJoin('s.categories', 'c')
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $this->categoryId)
        ;
    }

    private function applyTagFilter(QueryBuilder $qb): void
    {
        if (null === $this->tagId) {
            return;
        }

        $qb->innerJoin('s.tags', 't')
            ->andWhere('t.id = :tagId')
            ->setParameter('tagId', $this->tagId)
        ;
    }

    private function applySorting(QueryBuilder $qb): void
    {
        $needsGroupBy = false;
        $skuAlias = $this->getOrAddSkuJoin($qb);

        // Handle sales sorting
        if (null !== $this->salesSort) {
            $direction = 'sales_desc' === $this->salesSort ? 'DESC' : 'ASC';
            $qb->addSelect("SUM({$skuAlias}.salesReal + {$skuAlias}.salesVirtual) as HIDDEN salesTotal")
                ->addOrderBy('salesTotal', $direction)
            ;
            $needsGroupBy = true;
        }

        // Handle price sorting
        if (null !== $this->priceSort) {
            $direction = 'price_desc' === $this->priceSort ? 'DESC' : 'ASC';
            $aggregateFunc = 'price_desc' === $this->priceSort ? 'MAX' : 'MIN';
            $qb->addSelect("{$aggregateFunc}({$skuAlias}.marketPrice) as HIDDEN sortPrice")
                ->addOrderBy('sortPrice', $direction)
            ;
            $needsGroupBy = true;
        }

        // Add GROUP BY if aggregation is used
        if ($needsGroupBy) {
            $qb->groupBy('s.id');
        }

        $qb->addOrderBy('s.id', 'DESC');
    }

    /**
     * 创建计数查询构建器（简化版，不包含 fetch join）
     */
    private function createCountQueryBuilder(): QueryBuilder
    {
        return $this->spuRepository
            ->createQueryBuilder('s')
            ->select('COUNT(DISTINCT s.id)')
            ->andWhere('s.valid = true')
        ;
    }

    /**
     * 获取或添加SKU JOIN，统一管理SKU别名避免重复JOIN
     */
    private function getOrAddSkuJoin(QueryBuilder $qb): string
    {
        $aliases = $qb->getAllAliases();

        // 检查是否已存在SKU相关的JOIN
        if (in_array('sk', $aliases, true)) {
            return 'sk'; // 复用 createBaseQueryBuilder 中的别名
        }

        if (in_array('sku', $aliases, true)) {
            return 'sku'; // 复用 applyPriceFilter 中的别名
        }

        // 如果没有现有的SKU JOIN，添加新的
        $qb->leftJoin('s.skus', 'sort_sku');

        return 'sort_sku';
    }

    /**
     * 检查是否需要排序（销量或价格）
     */
    private function needsSorting(): bool
    {
        return null !== $this->salesSort || null !== $this->priceSort;
    }

    /**
     * 使用手动分页执行查询（避免 Doctrine 分页器限制）
     *
     * @return array{list: array<mixed>, pagination: array{current: int, pageSize: int, total: int, hasMore: bool}}
     */
    private function executeWithManualPagination(QueryBuilder $qb): array
    {
        // 获取总数（不带分页限制和排序）
        $countQb = $this->createCountQueryBuilder();

        if (null !== $this->keyword && '' !== $this->keyword) {
            $keyword = trim($this->keyword);
            $countQb->andWhere('s.title LIKE :keyword')
                ->setParameter('keyword', "%{$keyword}%")
            ;
        }

        $this->applyPriceFilter($countQb);
        $this->applyCategoryFilter($countQb);
        $this->applyTagFilter($countQb);

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        // 应用分页限制
        $offset = ($this->currentPage - 1) * $this->pageSize;
        $qb->setFirstResult($offset)
            ->setMaxResults($this->pageSize)
        ;

        // 执行查询
        $results = $qb->getQuery()->getResult();

        // 格式化结果
        $items = [];
        if (is_array($results)) {
            foreach ($results as $spu) {
                if ($spu instanceof Spu) {
                    $items[] = $this->formatItem($spu);
                }
            }
        }

        return [
            'list' => $items,
            'pagination' => [
                'current' => $this->currentPage,
                'pageSize' => $this->pageSize,
                'total' => $total,
                'hasMore' => ($offset + count($items)) < $total,
            ],
        ];
    }

    /**
     * 计算SKU价格信息
     *
     * @return array{minPrice: float|null, maxPrice: float|null, skuMinPrice: float|null, skuMaxPrice: float|null}
     */
    private function calculateSkuPrices(Sku $sku): array
    {
        // 使用市场价作为主要价格
        $marketPrice = $sku->getMarketPrice();
        $priceValue = null !== $marketPrice ? (float) $marketPrice : null;

        return [
            'minPrice' => $priceValue,
            'maxPrice' => $priceValue,
            'skuMinPrice' => $priceValue,
            'skuMaxPrice' => $priceValue,
        ];
    }

    /**
     * 格式化SKU数据
     *
     * @return array<string, mixed>
     */
    private function formatSkuData(Sku $sku, ?float $skuMinPrice, ?float $skuMaxPrice): array
    {
        $skuData = $this->formatterService->formatSkuArray($sku);
        $skuData['price'] = $skuMinPrice;
        $skuData['originalPrice'] = $skuMaxPrice;
        $skuData['salesReal'] = $sku->getSalesReal();
        $skuData['salesVirtual'] = $sku->getSalesVirtual();

        return $skuData;
    }

    /**
     * @return array<string, mixed>
     */
    private function formatItem(Spu $spu): array
    {
        $skus = $spu->getSkus();
        $minPrice = null;
        $maxPrice = null;
        $totalSales = 0;
        $totalStock = 0;
        $skuDataArray = [];

        foreach ($skus as $sku) {
            if (true !== $sku->isValid()) {
                continue;
            }

            $prices = $this->calculateSkuPrices($sku);

            // 更新全局最小最大价格
            if (null === $minPrice || (null !== $prices['minPrice'] && $prices['minPrice'] < $minPrice)) {
                $minPrice = $prices['minPrice'];
            }
            if (null === $maxPrice || (null !== $prices['maxPrice'] && $prices['maxPrice'] > $maxPrice)) {
                $maxPrice = $prices['maxPrice'];
            }

            $totalSales += $sku->getSalesReal() + $sku->getSalesVirtual();

            if ($this->includeSku) {
                $skuDataArray[] = $this->formatSkuData($sku, $prices['skuMinPrice'], $prices['skuMaxPrice']);
            }
        }

        $result = [
            'id' => $spu->getId(),
            'name' => $spu->getTitle(),
            'price' => $minPrice,
            'originalPrice' => $maxPrice,
            'mainThumb' => $this->formatterService->getSpuMainThumb($spu),
            'sales' => $totalSales,
            'stock' => $totalStock,
        ];

        if ($this->includeSku) {
            $result['skus'] = $skuDataArray;
        }

        return $result;
    }
}
