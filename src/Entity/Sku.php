<?php

namespace Tourze\ProductCoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Traits\CreatedFromIpAware;
use Tourze\DoctrinePrecisionBundle\Attribute\PrecisionColumn;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnumExtra\Itemable;
use Tourze\LockServiceBundle\Model\LockEntity;
use Tourze\ProductCoreBundle\Repository\SkuRepository;

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
 * @implements AdminArrayInterface<string, mixed>
 * @see https://support.google.com/merchants/answer/160161
 * @see https://support.google.com/merchants/answer/6324351
 * @see https://documentation.b2c.commercecloud.salesforce.com/DOC2/topic/com.demandware.dochelp/OCAPI/current/shop/Documents/Variant.html
 * @see https://learnku.com/articles/21461
 */
#[ORM\Table(name: 'product_sku', options: ['comment' => '产品SKU'])]
#[ORM\Entity(repositoryClass: SkuRepository::class)]
class Sku implements \Stringable, Itemable, AdminArrayInterface, LockEntity, \Tourze\ProductServiceContracts\SKU
{
    use BlameableAware;
    use TimestampableAware;
    use CreatedFromIpAware;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'SkuID'])]
    private int $id = 0;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Spu::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', inversedBy: 'skus')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Spu $spu = null;

    #[TrackColumn]
    #[SnowflakeColumn(prefix: 'SKU')]
    #[Assert\Length(max: 40, maxMessage: 'GTIN 不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 40, nullable: true, options: ['comment' => '全球唯一编码'])]
    private ?string $gtin = '';

    #[Assert\Length(max: 60, maxMessage: 'MPN 不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 60, nullable: true, options: ['comment' => '厂商型号码'])]
    private ?string $mpn = null;

    #[Assert\Length(max: 10, maxMessage: '单位不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 10, options: ['default' => '个', 'comment' => '单位'])]
    private ?string $unit = null;

    #[Assert\Type(type: 'bool', message: '需要收货必须为布尔值')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '需要收货', 'default' => 1])]
    private ?bool $needConsignee = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array', message: '图片必须为数组类型')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '图片'])]
    private ?array $thumbs = [];

    /**
     * @var Collection<int, SkuAttribute>
     */
    #[ORM\OneToMany(targetEntity: SkuAttribute::class, mappedBy: 'sku', cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $attributes;

    /**
     * @var Collection<int, SkuPackage>
     */
    #[ORM\OneToMany(targetEntity: SkuPackage::class, mappedBy: 'sku', cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $packages;

    #[Assert\Length(max: 255, maxMessage: '备注不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\Length(max: 255, maxMessage: '标题不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '规格标题'])]
    private ?string $title = null;

    #[Assert\PositiveOrZero(message: '真实销量不能为负数')]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => '0', 'comment' => '真实销量'])]
    private int $salesReal = 0;

    #[Assert\PositiveOrZero(message: '虚拟销量不能为负数')]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => '0', 'comment' => '虚拟销量'])]
    private int $salesVirtual = 0;

    #[Assert\Type(type: 'bool', message: '上架必须为布尔值')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '上架', 'default' => 1])]
    private ?bool $valid = true;

    #[PrecisionColumn]
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2, nullable: true, options: ['comment' => '市场价'])]
    #[Assert\PositiveOrZero(message: '市场价必须大于等于0')]
    private ?string $marketPrice = null;

    #[PrecisionColumn]
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2, nullable: true, options: ['comment' => '成本价'])]
    #[Assert\PositiveOrZero(message: '成本价必须大于等于0')]
    private ?string $costPrice = null;

    #[PrecisionColumn]
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2, nullable: true, options: ['comment' => '原价'])]
    #[Assert\PositiveOrZero(message: '原价必须大于等于0')]
    private ?string $originalPrice = null;

    #[Assert\NotBlank(message: '币种不能为空')]
    #[Assert\Length(max: 10, maxMessage: '币种长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 10, options: ['default' => 'CNY', 'comment' => '币种'])]
    private string $currency = 'CNY';

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '积分价格'])]
    #[Assert\PositiveOrZero(message: '积分价格必须大于等于0')]
    private ?int $integralPrice = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true, options: ['comment' => '税率(%)'])]
    #[Assert\Range(notInRangeMessage: '税率必须在 {{ min }}% 到 {{ max }}% 之间', min: 0, max: 100)]
    private ?float $taxRate = null;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->packages = new ArrayCollection();
    }

    public function __toString(): string
    {
        if ('0' === $this->getId()) {
            return '';
        }

        // 优先使用自定义标题
        if (null !== $this->getTitle() && '' !== $this->getTitle()) {
            return $this->getTitle();
        }

        // 回退到自动生成的名称
        return $this->getShorName();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    #[Ignore]
    public function getShorName(): string
    {
        $parts = [$this->getId()];
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

    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    public function setMpn(?string $mpn): void
    {
        $this->mpn = $mpn;
    }

    public function addAttribute(SkuAttribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
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

    /**
     * @param array<mixed> $form
     */
    public function beforeCurdSaveCheck(array $form): void
    {
        // TODO 同一SPU下的SKU不允许销售属性一模一样
    }

    public function addPackage(SkuPackage $package): self
    {
        if (!$this->packages->contains($package)) {
            $this->packages->add($package);
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

    /**
     * @return array<string, mixed>
     */
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
        $skuName = (null !== $this->getTitle() && '' !== $this->getTitle()) ? $this->getTitle() : $this->getShorName();

        return "{$this->getSpu()?->getTitle()} - {$skuName}";
    }

    public function getSpu(): ?Spu
    {
        return $this->spu;
    }

    public function setSpu(?Spu $spu): void
    {
        $this->spu = $spu;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setNeedConsignee(?bool $needConsignee): void
    {
        $this->needConsignee = $needConsignee;
    }

    /**
     * @return array<mixed>
     */
    public function renderPushStockButton(): array
    {
        // ModalFormAction functionality removed - AntdCpBundle not available
        return [];
        /*
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
                    ->setRules([['required' => true, 'message' => '请填写数量']),
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
        */
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

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getGtin(): ?string
    {
        return $this->gtin;
    }

    public function setGtin(?string $gtin): void
    {
        $this->gtin = $gtin;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
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

    /**
     * @return array<mixed>|null
     */
    public function getThumbs(): ?array
    {
        return $this->thumbs;
    }

    /**
     * @param array<mixed>|null $thumbs
     */
    public function setThumbs(?array $thumbs): void
    {
        $this->thumbs = $thumbs;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'gtin' => $this->getGtin(),
            'unit' => $this->getUnit(),
            'salesReal' => $this->getSalesReal(),
            'salesVirtual' => $this->getSalesVirtual(),
            'valid' => $this->isValid(),
            'needConsignee' => $this->isNeedConsignee(),
            'thumbs' => $this->getThumbs(),
            'salesCount' => $this->getSalesReal() + $this->getSalesVirtual(),
            'currency' => $this->getCurrency(),
            'integralPrice' => $this->getIntegralPrice(),
            'taxRate' => $this->getTaxRate(),
            'marketPrice' => $this->getMarketPrice(),
            'costPrice' => $this->getCostPrice(),
            'originalPrice' => $this->getOriginalPrice(),
        ];
    }

    public function retrieveLockResource(): string
    {
        return 'sku_id_' . $this->getId();
    }

    public function getMarketPrice(): ?string
    {
        return $this->marketPrice;
    }

    public function setMarketPrice(?string $marketPrice): void
    {
        $this->marketPrice = $marketPrice;
    }

    public function getCostPrice(): ?string
    {
        return $this->costPrice;
    }

    public function setCostPrice(?string $costPrice): void
    {
        $this->costPrice = $costPrice;
    }

    public function getOriginalPrice(): ?string
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(?string $originalPrice): void
    {
        $this->originalPrice = $originalPrice;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveSkuArray(): array
    {
        $attributes = [];
        foreach ($this->getAttributes() as $attribute) {
            $attributes[] = $attribute->retrieveSkuArray();
        }

        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'mainThumb' => $this->getMainThumb(),
            'thumbs' => $this->getThumbs(),
            'unit' => $this->getUnit(),
            'gtin' => $this->getGtin(),
            'valid' => $this->isValid(),
            'attributes' => $attributes,
            'displayAttribute' => $this->getDisplayAttribute(),
            'salesCount' => $this->getSalesCount(),
            'marketPrice' => $this->getMarketPrice(),
            'costPrice' => $this->getCostPrice(),
            'originalPrice' => $this->getOriginalPrice(),
            'currency' => $this->getCurrency(),
            'integralPrice' => $this->getIntegralPrice(),
            'taxRate' => $this->getTaxRate(),
        ];
    }

    /**
     * 获取主缩略图
     */
    public function getMainThumb(): string
    {
        // 尝试从自己的缩略图获取
        $myThumb = $this->getFirstThumbUrl($this->getThumbs());
        if ('' !== $myThumb) {
            return $myThumb;
        }

        // 尝试从SPU获取
        $spu = $this->getSpu();
        if (null === $spu) {
            return '';
        }

        $mainPic = $spu->getMainPic();
        if (null !== $mainPic && '' !== $mainPic) {
            return $mainPic;
        }

        return $this->getFirstThumbUrl($spu->getThumbs());
    }

    /**
     * 从缩略图数组中获取第一个URL
     * @param array<mixed>|null $thumbs
     */
    private function getFirstThumbUrl(?array $thumbs): string
    {
        if (null === $thumbs || [] === $thumbs) {
            return '';
        }

        $firstThumb = $thumbs[0] ?? null;
        if (is_array($firstThumb) && isset($firstThumb['url'])) {
            $url = $firstThumb['url'];
            if (is_string($url)) {
                return $url;
            }
            if (is_numeric($url)) {
                return (string) $url;
            }
            if (is_bool($url)) {
                return '';
            }
            if (is_object($url) || is_array($url)) {
                return '';
            }
            if (is_resource($url)) {
                return '';
            }

            // 对于无法处理的其他类型，返回空字符串
            return '';
        }

        return '';
    }

    /**
     * 获取显示属性（商品规格）
     */
    public function getDisplayAttribute(): string
    {
        $res = [];
        foreach ($this->getAttributes() as $attribute) {
            if (in_array($attribute->getName(), ['itemCode', 'itemID', 'itemTitle', 'shopNick', 'skuTitle', 'storeID'], true)) {
                continue;
            }

            $res[] = "{$attribute->getName()}{$attribute->getValue()}";
        }

        if ([] === $res) {
            return (string) $this->getGtin();
        }

        return implode('+', $res);
    }

    /**
     * 获取销量总计
     */
    private function getSalesCount(): int
    {
        return $this->getSalesReal() + $this->getSalesVirtual();
    }

    /**
     * 获取结账用的数组表示
     *
     * @return array<string, mixed>
     */
    public function retrieveCheckoutArray(): array
    {
        return [
            'id' => $this->getId(),
            'gtin' => $this->getGtin(),
            'mpn' => $this->getMpn(),
            'unit' => $this->getUnit(),
            'thumbs' => $this->getThumbs(),
            'currency' => $this->getCurrency(),
            'integralPrice' => $this->getIntegralPrice(),
            'taxRate' => $this->getTaxRate(),
        ];
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getIntegralPrice(): ?int
    {
        return $this->integralPrice;
    }

    public function setIntegralPrice(?int $integralPrice): void
    {
        $this->integralPrice = $integralPrice;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): void
    {
        $this->taxRate = $taxRate;
    }
}
