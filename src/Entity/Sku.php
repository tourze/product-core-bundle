<?php

namespace ProductBundle\Entity;

use AntdCpBundle\Builder\Action\ModalFormAction;
use AntdCpBundle\Builder\Field\DynamicFieldSet;
use AntdCpBundle\Builder\Field\InputNumberField;
use AntdCpBundle\Builder\Field\LongTextField;
use App\Kernel;
use AppBundle\Service\CurrencyManager;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ProductBundle\Enum\PriceType;
use ProductBundle\Enum\StockChange;
use ProductBundle\Repository\SkuRepository;
use ProductBundle\Service\StockService;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\CurdAction;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\Exportable;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Action\ListAction;
use Tourze\EasyAdmin\Attribute\Event\BeforeCreate;
use Tourze\EasyAdmin\Attribute\Event\BeforeEdit;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EnumExtra\Itemable;
use Tourze\LockServiceBundle\Model\LockEntity;
use Yiisoft\Arrays\ArraySorter;

/**
 * 在国外的电商系统，SKU 有时候也叫为变体（variant）
 * 参考https://support.google.com/merchants/answer/160161 增加了 GTIN 和 MPN 字段
 * GTIN 可以是 UPC码、EAN码，或者我们平时讲的69码
 * MPN 则是品牌分配的唯一编码
 * 例子可以参考：https://support.google.com/merchants/answer/6324351
 * 在一些场景中，我们可能没有 GTIN，那么可以使用 品牌+MPN来做唯一标志。
 * 如果这个SKU是个套装，并且套装SKU没有申请GTIN的话，需提交主商品的GTIN
 *
 * TODO 参考电信计费模型，SKU应该也要use LimitAware
 *
 * @see https://support.google.com/merchants/answer/160161
 * @see https://support.google.com/merchants/answer/6324351
 * @see https://documentation.b2c.commercecloud.salesforce.com/DOC2/topic/com.demandware.dochelp/OCAPI/current/shop/Documents/Variant.html
 * @see https://learnku.com/articles/21461
 */
#[Exportable(label: '导出')]
#[Listable]
#[Editable(drawerWidth: 850)]
#[Creatable(drawerWidth: 850)]
#[ORM\Table(name: 'product_sku', options: ['comment' => '产品SKU'])]
#[ORM\Entity(repositoryClass: SkuRepository::class)]
class Sku implements \Stringable, Itemable, AdminArrayInterface, LockEntity
{
        use BlameableAware;
    use TimestampableAware;


    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'SkuID'])]
    private ?int $id = 0;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Spu::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', inversedBy: 'skus')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Spu $spu = null;
    use StockValueAware;
    /**
     * 全球唯一编码，可以是UPC、EAN、69码等等.
     */
    #[TrackColumn]
    #[SnowflakeColumn(prefix: 'SKU')]
    #[ORM\Column(type: Types::STRING, length: 40, nullable: true, options: ['comment' => '全球唯一编码'])]
    private ?string $gtin = '';
    /**
     * 制造商部件号，一般可以理解为型号，或其他自编码
     */
    #[ORM\Column(type: Types::STRING, length: 60, nullable: true, options: ['comment' => '厂商型号码'])]
    private ?string $mpn = null;
    #[ORM\Column(type: Types::STRING, length: 10, options: ['default' => '个', 'comment' => '单位'])]
    private ?string $unit = null;
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '需要收货', 'default' => 1])]
    private ?bool $needConsignee = null;
    /**
     * @var Collection<Price>
     */
    #[CurdAction(label: '定价', drawerWidth: 1300)]
    #[Groups(['admin_curd'])]
    #[ORM\OneToMany(mappedBy: 'sku', targetEntity: Price::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $prices;
    #[ImagePickerField(limit: 9)]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '图片'])]
    private ?array $thumbs = [];
    /**
     * @DynamicFieldSet()
     *
     * @var Collection<SkuAttribute>
     */
    #[ORM\OneToMany(mappedBy: 'sku', targetEntity: SkuAttribute::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $attributes;
    /**
     * @DynamicFieldSet()
     *
     * @var Collection<SkuPackage>
     */
    #[ORM\OneToMany(mappedBy: 'sku', targetEntity: SkuPackage::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $packages;
    /**
     * @var Collection<int, Stock>|Stock[]
     */
    #[ORM\OneToMany(mappedBy: 'sku', targetEntity: Stock::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $stocks;
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;
    /**
     * @var Collection<int, StockLog>|StockLog[]
     */
    #[ORM\OneToMany(mappedBy: 'sku', targetEntity: StockLog::class)]
    private Collection $stockLogs;
    /**
     * @DynamicFieldSet()
     *
     * @var Collection<SkuLimitRule>
     */
    #[ORM\OneToMany(mappedBy: 'sku', targetEntity: SkuLimitRule::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $limitRules;
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => '0', 'comment' => '真实销量'])]
    private int $salesReal = 0;
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => '0', 'comment' => '虚拟销量'])]
    private int $salesVirtual = 0;
    #[Groups(['admin_curd', 'restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '发货期限'])]
    private ?int $dispatchPeriod = null;
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '上架', 'default' => 1])]
    private ?bool $valid = true;
    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;
    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->packages = new ArrayCollection();
        $this->stockLogs = new ArrayCollection();
        $this->limitRules = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        // return "{$this->getSpu()->getTitle()} - {$this->getShorName()}";
        return $this->getShorName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Ignore]
    public function getShorName(): string
    {
        $parts = [];
        foreach ($this->getAttributes() as $attr) {
            $parts[] = $attr->getValue();
        }

        return implode(' - ', $parts);
    }

    /**
     * @return Collection<int, SkuAttribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    public function setMpn(?string $mpn): self
    {
        $this->mpn = $mpn;

        return $this;
    }

    public function addPrice(Price $price): self
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->setSku($this);
        }

        return $this;
    }

    public function removePrice(Price $price): self
    {
        if ($this->prices->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getSku() === $this) {
                $price->setSku(null);
            }
        }

        return $this;
    }

    /**
     * @return array|Price[]
     */
    public function getSortedPrices(): array
    {
        $now = Carbon::now();

        $list = $this->getPrices()
            ->filter(function (Price $price) use ($now): bool {
                if ($price->getEffectTime() && $now->lessThan($price->getEffectTime())) {
                    return false;
                }
                if ($price->getExpireTime() && $now->greaterThan($price->getExpireTime())) {
                    return false;
                }

                return true;
            })
            ->toArray();
        // 先通过序号倒序，再根据ID顺序
        ArraySorter::multisort($list, [
            fn (Price $item) => $item->getPriority(),
            fn (Price $item) => $item->getId(),
        ], [SORT_DESC, SORT_ASC]);

        return $list;
    }

    /**
     * @return Collection<int, Price>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addAttribute(SkuAttribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
            $attribute->setSku($this);
        }

        return $this;
    }

    public function removeAttribute(SkuAttribute $attribute): self
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getSku() === $this) {
                $attribute->setSku(null);
            }
        }

        return $this;
    }

    #[BeforeCreate]
    #[BeforeEdit]
    public function beforeCurdSaveCheck(array $form): void
    {
        // TODO 同一SPU下的SKU不允许销售属性一模一样
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $commodity): self
    {
        if (!$this->stocks->contains($commodity)) {
            $this->stocks[] = $commodity;
            $commodity->setSku($this);
        }

        return $this;
    }

    public function removeStock(Stock $commodity): self
    {
        if ($this->stocks->removeElement($commodity)) {
            // set the owning side to null (unless already changed)
            if ($commodity->getSku() === $this) {
                $commodity->setSku(null);
            }
        }

        return $this;
    }

    /**
     * 获取实际成本价格（内部管理啦）.
     */
    #[Ignore]
    public function getCostPrice(): ?Price
    {
        $price = $this->getPrices()
            ->filter(fn (Price $price) => PriceType::COST === $price->getType())
            ->first();

        return $price ?: null;
    }

    public function addPackage(SkuPackage $package): self
    {
        if (!$this->packages->contains($package)) {
            $this->packages[] = $package;
            $package->setSku($this);
        }

        return $this;
    }

    public function removePackage(SkuPackage $package): self
    {
        if ($this->packages->removeElement($package)) {
            // set the owning side to null (unless already changed)
            if ($package->getSku() === $this) {
                $package->setSku(null);
            }
        }

        return $this;
    }

    public function toSelectItem(): array
    {
        return [
            'label' => $this->getFullName(),
            'text' => $this->getFullName(),
            'value' => $this->getId(),
        ];
    }

    #[Ignore]
    public function getFullName(): string
    {
        return "{$this->getSpu()->getTitle()} - {$this->getShorName()}";
    }

    public function getSpu(): ?Spu
    {
        return $this->spu;
    }

    public function setSpu(?Spu $spu): self
    {
        $this->spu = $spu;

        return $this;
    }


    public function renderCurrentPrice(): array
    {
        $prices = $this->determineOnTimeSalePrice(Carbon::now());
        $rs = [];
        foreach ($prices as $price) {
            $currency = $price->renderCurrency();

            $rs[] = [
                'text' => trim("{$price->getType()->getLabel()} {$price->getPrice()}{$currency}"),
                'fontStyle' => ['fontSize' => 13],
            ];
        }

        return $rs;
    }

    /**
     * 根据指定时间来获取当前有效的价格信息.
     *
     * @return array|Price[]
     */
    public function determineOnTimeSalePrice(CarbonInterface $now): array
    {
        return $this->getPrices()
            ->filter(function (Price $price) use ($now): bool {
                if (PriceType::SALE !== $price->getType()) {
                    return false;
                }

                return $price->checkByDateTime($now);
            })
            ->toArray();
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }

    public function setNeedConsignee(?bool $needConsignee): self
    {
        $this->needConsignee = $needConsignee;

        return $this;
    }

    /**
     * @return Collection<int, StockLog>
     */
    public function getStockLogs(): Collection
    {
        return $this->stockLogs;
    }

    public function addStockLog(StockLog $stockLog): self
    {
        if (!$this->stockLogs->contains($stockLog)) {
            $this->stockLogs[] = $stockLog;
            $stockLog->setSku($this);
        }

        return $this;
    }

    public function removeStockLog(StockLog $stockLog): self
    {
        if ($this->stockLogs->removeElement($stockLog)) {
            // set the owning side to null (unless already changed)
            if ($stockLog->getSku() === $this) {
                $stockLog->setSku(null);
            }
        }

        return $this;
    }

    #[ListAction(title: '入库', showExpression: '!hasEnv("FIXED_PRODUCT_STOCK_NUMBER")')]
    public function renderPushStockButton(): ModalFormAction
    {
        return ModalFormAction::gen()
            ->setFormTitle('入库')
            ->setLabel('入库')
            ->setFormFields([
                InputNumberField::gen()
                    ->setId('stockQuantity')
                    ->setLabel('数量')
                    ->setInputProps([
                        'style' => [
                            'width' => '50%',
                        ],
                    ])
                    ->setRules([['required' => true, 'message' => '请填写数量']]),
                LongTextField::gen()
                    ->setId('stockRemark')
                    ->setLabel('备注'),
            ])
            ->setCallback(function (
                array $form,
                array $record,
                StockService $stockService,
            ) {
                $log = new StockLog();
                $log->setType(StockChange::PUT);
                $log->setRemark($form['stockRemark']);
                $log->setSku($this);
                $log->setQuantity($form['stockQuantity']);
                $stockService->process($log);

                return [
                    '__message' => '入库成功',
                    'form' => $form,
                    'record' => $record,
                ];
            });
    }

    public function isBundle(): bool
    {
        // 有配置打包信息，才算是套餐 / 打包品
        return $this->getPackages()->count() > 0;
    }

    /**
     * @return Collection<int, SkuPackage>
     */
    public function getPackages(): Collection
    {
        return $this->packages;
    }

    public function addLimitRule(SkuLimitRule $skuLimitRule): self
    {
        if (!$this->limitRules->contains($skuLimitRule)) {
            $this->limitRules->add($skuLimitRule);
            $skuLimitRule->setSku($this);
        }

        return $this;
    }

    public function removeLimitRule(SkuLimitRule $skuLimitRule): self
    {
        if ($this->limitRules->removeElement($skuLimitRule)) {
            // set the owning side to null (unless already changed)
            if ($skuLimitRule->getSku() === $this) {
                $skuLimitRule->setSku(null);
            }
        }

        return $this;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'gtin' => $this->getGtin(),
            'unit' => $this->getUnit(),
            'salesReal' => $this->getSalesReal(),
            'salesVirtual' => $this->getSalesVirtual(),
            'valid' => $this->isValid(),
            'validStock' => $this->getValidStock(),
            'virtualStock' => $this->getVirtualStock(),
            'needConsignee' => $this->isNeedConsignee(),
            'thumbs' => $this->getThumbs(),
            'dispatchPeriod' => $this->getDispatchPeriod(),
            'displayAttribute' => $this->getDisplayAttribute(),
            'displayPrice' => $this->getDisplayPrice(),
            'displayTaxPrice' => $this->getDisplayTaxPrice(),
            'displayTax' => $this->getDisplayTax(),
            'displaySalePrice' => $this->getDisplaySalePrice(),
            'displayOriginalPrice' => $this->getDisplayOriginalPrice(),
            'mainThumb' => $this->getMainThumb(),
            'stockCount' => $this->getStockCount(),
            'salesCount' => $this->getSalesCount(),
            'limitConfig' => $this->getLimitConfig(),
            'totalSale' => $this->getTotalSale(),
        ];
    }public function getGtin(): ?string
    {
        return $this->gtin;
    }

    public function setGtin(?string $gtin): self
    {
        $this->gtin = $gtin;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getSalesReal(): int
    {
        return $this->salesReal;
    }

    public function setSalesReal(int $salesReal): void
    {
        $this->salesReal = $salesReal;
    }

    public function getSalesVirtual(): int
    {
        return $this->salesVirtual;
    }

    public function setSalesVirtual(int $salesVirtual): void
    {
        $this->salesVirtual = $salesVirtual;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function isNeedConsignee(): ?bool
    {
        return $this->needConsignee;
    }

    public function getThumbs(): ?array
    {
        return $this->thumbs;
    }

    public function setThumbs(?array $thumbs): self
    {
        $this->thumbs = $thumbs;

        return $this;
    }

    public function getDispatchPeriod(): ?int
    {
        return $this->dispatchPeriod;
    }

    public function setDispatchPeriod(?int $dispatchPeriod): static
    {
        $this->dispatchPeriod = $dispatchPeriod;

        return $this;
    }

    /**
     * 获取用于最终显示的规格数据.
     */
    public function getDisplayAttribute(): string
    {
        $res = [];
        foreach ($this->getAttributes() as $attribute) {
            if (in_array($attribute->getName(), ['itemCode', 'itemID', 'itemTitle', 'shopNick', 'skuTitle', 'storeID'])) {
                continue;
            }

            $res[] = "{$attribute->getName()}{$attribute->getValue()}";
        }

        if (empty($res)) {
            return (string) $this->getGtin();
        }

        return implode('+', $res);
    }

    /**
     * SKU的 未含税价格
     */
    public function getDisplayPrice(): string
    {
        $result = [
            // 币种 => [ 价格1, 价格2 ],
        ];
        foreach ($this->getSalePrices() as $price) {
            if (!isset($result[$price->getCurrency()])) {
                $result[$price->getCurrency()] = [];
            }
            $result[$price->getCurrency()][] = Kernel::container()->get(CurrencyManager::class)->getDisplayPrice($price->getCurrency(), $price->getPrice());
        }
        foreach ($result as $k => $v) {
            // 因为可能多种类型价格，所以使用 / 来分割
            $result[$k] = implode('/', $v);
        }

        return implode('+', $result);
    }

    /**
     * @return Price[]|array
     */
    #[Groups(['restful_read'])]
    public function getSalePrices(): array
    {
        // 只看销售价格
        return $this->getPrices()
            ->filter(fn (Price $price) => PriceType::SALE === $price->getType())
            ->toArray();
    }

    /**
     * SKU的 含税价格
     */
    public function getDisplayTaxPrice(): string
    {
        $result = [
            // 币种 => [ 价格1, 价格2 ],
        ];
        foreach ($this->getSalePrices() as $price) {
            if (!isset($result[$price->getCurrency()])) {
                $result[$price->getCurrency()] = [];
            }
            $result[$price->getCurrency()][] = Kernel::container()->get(CurrencyManager::class)->getDisplayPrice($price->getCurrency(), $price->getTaxPrice());
        }
        foreach ($result as $k => $v) {
            // 因为可能多种类型价格，所以使用 / 来分割
            $result[$k] = implode('/', $v);
        }

        return implode('+', $result);
    }

    /**
     * SKU的 含税价格
     */
    public function getDisplayTax(): string
    {
        $result = [
            // 币种 => [ 价格1, 价格2 ],
        ];
        foreach ($this->getSalePrices() as $price) {
            if (!isset($result[$price->getCurrency()])) {
                $result[$price->getCurrency()] = [];
            }
            $result[$price->getCurrency()][] = Kernel::container()->get(CurrencyManager::class)->getDisplayPrice($price->getCurrency(), $price->getTax());
        }
        foreach ($result as $k => $v) {
            $result[$k] = implode('/', $v);
        }

        return implode('+', $result);
    }

    /**
     * 后端计算好价格给前端
     */
    public function getDisplaySalePrice(): string
    {
        return $this->getDisplayPrice();
    }

    public function getDisplayOriginalPrice(): ?string
    {
        $price = $this->getPrices()
            ->filter(fn (Price $price) => PriceType::ORIGINAL_PRICE === $price->getType())
            ->first();

        return $price !== null ? $price->getPrice()  : null;
    }

    /**
     * 返回缩略图.
     */
    #[Groups(['restful_read'])]
    public function getMainThumb(): string
    {
        if (!$this->getThumbs()) {
            return $this->getSpu()->getMainThumb();
        }

        return $this->getThumbs()[0]['url'];
    }

    /**
     * 获取库存数量，这里返回的是真实+虚拟库存.
     */
    #[Groups(['restful_read'])]
    public function getStockCount(): int
    {
        if (isset($_ENV['FIXED_PRODUCT_STOCK_NUMBER'])) {
            return intval($_ENV['FIXED_PRODUCT_STOCK_NUMBER']);
        }

        return $this->getVirtualStock() + $this->getValidStock();
    }

    /**
     * 获取销量 ，真实+虚拟
     */
    #[Groups(['restful_read'])]
    public function getSalesCount(): int
    {
        return $this->getSalesReal() + $this->getSalesVirtual();
    }

    #[Groups(['restful_read'])]
    public function getLimitConfig(): ?array
    {
        if ($this->getLimitRules()->isEmpty()) {
            return null;
        }

        $result = [];
        foreach ($this->getLimitRules() as $limitRule) {
            $result[$limitRule->getType()->value] = $limitRule->getValue();
        }

        return $result;
    }

    /**
     * @return Collection<int, SkuLimitRule>
     */
    public function getLimitRules(): Collection
    {
        return $this->limitRules;
    }

    #[Groups(['restful_read', 'admin_curd'])]
    public function getTotalSale(): int
    {
        return $this->getSalesReal() + $this->getSalesVirtual();
    }

    public function retrieveSkuArray(): array
    {
        $salePrices = [];
        foreach ($this->getSalePrices() as $price) {
            $salePrices[] = $price->retrieveSkuArray();
        }

        $attributes = [];
        foreach ($this->getAttributes() as $attribute) {
            $attributes[] = $attribute->retrieveSkuArray();
        }

        return [
            'id' => $this->getId(),
            'mainThumb' => $this->getMainThumb(),
            'thumbs' => $this->getThumbs(),
            'unit' => $this->getUnit(),
            'gtin' => $this->getGtin(),
            'dispatchPeriod' => $this->getDispatchPeriod(),
            'valid' => $this->isValid(),
            'attributes' => $attributes,
            'displayAttribute' => $this->getDisplayAttribute(),
            'displayPrice' => $this->getDisplayPrice(),
            'displayTax' => $this->getDisplayTax(),
            'displayTaxPrice' => $this->getDisplayTaxPrice(),
            'displaySalePrice' => $this->getDisplaySalePrice(),
            'displayOriginalPrice' => $this->getDisplayOriginalPrice(),
            'stockCount' => $this->getStockCount(),
            'salesCount' => $this->getSalesCount(),
            'limitConfig' => $this->getLimitConfig(),
            'salePrices' => $salePrices,
        ];
    }

    public function retrieveCheckoutArray(): array
    {
        $attributes = [];
        foreach ($this->getAttributes() as $attribute) {
            $attributes[] = $attribute->retrieveSpuArray();
        }

        $salePrices = [];
        foreach ($this->getSalePrices() as $price) {
            $salePrices[] = $price->retrieveSpuArray();
        }

        return [
            'id' => $this->getId(),
            'gtin' => $this->getGtin(),
            'unit' => $this->getUnit(),
            'thumbs' => $this->getThumbs(),
            'attributes' => $attributes,
            'dispatchPeriod' => $this->getDispatchPeriod(),
            'valid' => $this->isValid(),
            'displayAttribute' => $this->getDisplayAttribute(),
            'salePrices' => $salePrices,
            'displayPrice' => $this->getDisplayPrice(),
            'displayTaxPrice' => $this->getDisplayTaxPrice(),
            'displayTax' => $this->getDisplayTax(),
            'mainThumb' => $this->getMainThumb(),
            'stockCount' => $this->getStockCount(),
            'salesCount' => $this->getSalesCount(),
            'limitConfig' => $this->getLimitConfig(),
        ];
    }

    public function retrieveSpuArray(): array
    {
        $salePrices = [];
        foreach ($this->getSalePrices() as $price) {
            $salePrices[] = $price->retrieveSpuArray();
        }

        $attributes = [];
        foreach ($this->getAttributes() as $attribute) {
            $attributes[] = $attribute->retrieveSpuArray();
        }

        return [
            'id' => $this->getId(),
            'gtin' => $this->getGtin(),
            'unit' => $this->getUnit(),
            'needConsignee' => $this->isNeedConsignee(),
            'thumbs' => $this->getThumbs(),
            'attributes' => $attributes,
            'dispatchPeriod' => $this->getDispatchPeriod(),
            'valid' => $this->isValid(),
            'displayAttribute' => $this->getDisplayAttribute(),
            'salePrices' => $salePrices,
            'displayPrice' => $this->getDisplayPrice(),
            'displayTaxPrice' => $this->getDisplayTaxPrice(),
            'displayTax' => $this->getDisplayTax(),
            'displaySalePrice' => $this->getDisplaySalePrice(),
            'displayOriginalPrice' => $this->getDisplayOriginalPrice(),
            'mainThumb' => $this->getMainThumb(),
            'stockCount' => $this->getStockCount(),
            'salesCount' => $this->getSalesCount(),
            'limitConfig' => $this->getLimitConfig(),
            'totalSale' => $this->getTotalSale(),
        ];
    }

    public function retrieveLockResource(): string
    {
        return 'sku_id_' . $this->getId();
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }
}
