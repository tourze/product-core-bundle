<?php

namespace Tourze\ProductCoreBundle\Entity;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrinePrecisionBundle\Attribute\PrecisionColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductCoreBundle\Enum\PriceType;
use Tourze\ProductCoreBundle\Repository\PriceRepository;

/**
 * 价格
 *
 * 同币种的话，则取最优惠那个；不同币种的话，则相加。
 *
 * @see https://www.cnblogs.com/Ivan-j2ee/archive/2012/10/24/2737975.html
 * @see https://blog.csdn.net/hezhipin610039/article/details/6903255
 * @see https://ofbiz.apache.org/ofbiz-demos.html
 * @see https://blog.csdn.net/yu15163158717/article/details/80981125
 * @see http://www.woshipm.com/pd/2893875.html
 * @see https://cloud.tencent.com/developer/article/1866203
 */
#[ORM\Table(name: 'product_price', options: ['comment' => '产品价格'])]
#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price implements \Stringable, AdminArrayInterface
{
        use BlameableAware;
    use TimestampableAware;


    #[Groups(groups: ['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '价格ID'])]
    private ?int $id = 0;
    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Sku::class, cascade: ['persist'], inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Sku $sku = null;
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 60, enumType: PriceType::class, options: ['default' => 'sale', 'comment' => '类型'])]
    private PriceType $type;
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 10, options: ['default' => 'CNY', 'comment' => '币种'])]
    private ?string $currency = null;
    /**
     * @var string|null 我们理解这个金额不包含税费
     */
    #[PrecisionColumn]
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2, options: ['comment' => '金额'])]
    private ?string $price = null;
    #[ORM\Column(type: Types::FLOAT, nullable: true, options: ['comment' => '税率(%)'])]
    private ?float $taxRate = null;
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => '公式'])]
    private ?string $formula = null;
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '允许退款', 'default' => 1])]
    private ?bool $canRefund = true;
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '优先级', 'default' => 0])]
    private ?int $priority = null;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '生效时间'])]
    private ?\DateTimeInterface $effectTime = null;
    #[Assert\GreaterThan(propertyPath: 'effectTime')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '过期时间'])]
    private ?\DateTimeInterface $expireTime = null;
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否默认'])]
    private ?bool $isDefault = false;
    #[ORM\Column(nullable: true, options: ['comment' => '最小购买数量'])]
    private ?int $minBuyQuantity = null;
    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;
    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __toString(): string
    {
        if ($this->getId() === null || $this->getId() === 0) {
            return '';
        }

        $dateRange = $this->getDateRange();

        return sprintf('%s %s %s%s', $dateRange, $this->getType()->getLabel(), $this->getCurrency(), $this->getPrice());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRange(): string
    {
        $startDate = CarbonImmutable::parse($this->getEffectTime());
        $endDate = CarbonImmutable::parse($this->getExpireTime());

        return self::getHumanizeDayText($startDate, $endDate);
    }

    public function getEffectTime(): ?\DateTimeInterface
    {
        return $this->effectTime;
    }

    public function setEffectTime(?\DateTimeInterface $effectTime): self
    {
        $this->effectTime = $effectTime;

        return $this;
    }

    public function getExpireTime(): ?\DateTimeInterface
    {
        return $this->expireTime;
    }

    public function setExpireTime(?\DateTimeInterface $expireTime): self
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    /**
     * 获取一个更加好阅读的时间字段文本
     */
    private static function getHumanizeDayText(CarbonInterface $startDate, CarbonInterface $endDate): string
    {
        if ($startDate->isSameDay($endDate)) {
            return $startDate->format('Y-m-d');
        }

        if ($startDate->isSameMonth($endDate)) {
            return "{$startDate->format('Y-m-d')}至{$endDate->format('d')}";
        }

        if ($startDate->isSameYear($endDate)) {
            return "{$startDate->format('Y-m-d')}至{$endDate->format('m-d')}";
        }

        $a = $startDate->format('Y-m-d');
        $b = $endDate->format('Y-m-d');
        // 如果开始和结束时间相同的话，返回一个就好
        if ($a === $b) {
            return $a;
        }

        return "{$a}至{$b}";
    }

    public function getType(): PriceType
    {
        return $this->type;
    }

    public function setType(PriceType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
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

    public function renderCurrency(): string
    {
        // Currency service integration removed - AppBundle not available
        return strval($this->getCurrency());
    }

    public function getSku(): ?Sku
    {
        return $this->sku;
    }

    public function setSku(?Sku $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getFormula(): ?string
    {
        return $this->formula;
    }

    public function setFormula(?string $formula): self
    {
        $this->formula = $formula;

        return $this;
    }

    public function validDateRange(): void
    {
        // TODO 如果是销售价格，我们要确保同一时间点，不会有多个销售价格
        // TODO 要注意的话，上面的校验，是要区分currency的。因为有些商品可能今天是100RMB+100积分，明天变成100RMB+200积分
    }

    public function setCanRefund(?bool $canRefund): self
    {
        $this->canRefund = $canRefund;

        return $this;
    }

    public function isIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(?bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * 检查当前价格是否在时间内有效
     */
    public function checkByDateTime(CarbonInterface $now): bool
    {
        if ($this->getEffectTime() !== null && $now->lessThan($this->getEffectTime())) {
            return false;
        }
        if ($this->getExpireTime() !== null && $now->greaterThan($this->getExpireTime())) {
            return false;
        }

        return true;
    }

    public function retrieveSpuArray(): array
    {
        return [
            'id' => $this->getId(),
            'currency' => $this->getCurrency(),
            'price' => $this->getPrice(),
            'taxRate' => $this->getTaxRate(),
            'minBuyQuantity' => $this->getMinBuyQuantity(),
            'displayPrice' => $this->getDisplayPrice(),
            'tax' => $this->getTax(),
            'displayTax' => $this->getDisplayTax(),
            'taxPrice' => $this->getTaxPrice(),
            'displayTaxPrice' => $this->getDisplayTaxPrice(),
        ];
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getMinBuyQuantity(): ?int
    {
        return $this->minBuyQuantity;
    }

    public function setMinBuyQuantity(?int $minBuyQuantity): static
    {
        $this->minBuyQuantity = $minBuyQuantity;

        return $this;
    }

    /**
     * 未含税价格
     */
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    public function getDisplayPrice(): string
    {
        // CurrencyManager integration removed - AppBundle not available
        return $this->getCurrency() . ' ' . $this->getPrice();
    }

    /**
     * 税费
     */
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    public function getTax(): float
    {
        // CurrencyManager integration removed - AppBundle not available
        $price = floatval($this->getPrice() ?? 0);
        $taxRate = $this->getTaxRate() ?? 0;
        return round($price * ($taxRate / 100), 2);
    }

    /**
     * 税费
     */
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    public function getDisplayTax(): string
    {
        // CurrencyManager integration removed - AppBundle not available
        return $this->getCurrency() . ' ' . number_format($this->getTax(), 2);
    }

    /**
     * 含税价格
     */
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    public function getTaxPrice(): float
    {
        // CurrencyManager integration removed - AppBundle not available
        $price = floatval($this->getPrice() ?? 0);
        return round($price + $this->getTax(), 2);
    }

    /**
     * 含税价格
     */
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    public function getDisplayTaxPrice(): string
    {
        // CurrencyManager integration removed - AppBundle not available
        return $this->getCurrency() . ' ' . number_format($this->getTaxPrice(), 2);
    }

    public function retrieveSkuArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType()->value,
            'currency' => $this->getCurrency(),
            'price' => $this->getPrice(),
            'taxRate' => $this->getTaxRate(),
            'minBuyQuantity' => $this->getMinBuyQuantity(),
            'displayPrice' => $this->getDisplayPrice(),
            'tax' => $this->getTax(),
            'displayTax' => $this->getDisplayTax(),
            'taxPrice' => $this->getTaxPrice(),
            'displayTaxPrice' => $this->getDisplayTaxPrice(),
        ];
    }

    public function retrieveCheckoutArray(): array
    {
        return [
            'id' => $this->getId(),
            'currency' => $this->getCurrency(),
            'price' => $this->getPrice(),
            'taxRate' => $this->getTaxRate(),
            'minBuyQuantity' => $this->getMinBuyQuantity(),
            'displayPrice' => $this->getDisplayPrice(),
            'tax' => $this->getTax(),
            'displayTax' => $this->getDisplayTax(),
            'taxPrice' => $this->getTaxPrice(),
            'displayTaxPrice' => $this->getDisplayTaxPrice(),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'currency' => $this->getCurrency(),
            'price' => $this->getPrice(),
            'taxRate' => $this->getTaxRate(),
            'minBuyQuantity' => $this->getMinBuyQuantity(),
            'displayPrice' => $this->getDisplayPrice(),
            'tax' => $this->getTax(),
            'displayTax' => $this->getDisplayTax(),
            'taxPrice' => $this->getTaxPrice(),
            'displayTaxPrice' => $this->getDisplayTaxPrice(),
            'type' => $this->getType(),
            'canRefund' => $this->isCanRefund(),
            'priority' => $this->getPriority(),
            'effectTime' => $this->getEffectTime()?->format('Y-m-d H:i:s'),
            'expireTime' => $this->getExpireTime()?->format('Y-m-d H:i:s'),
            'remark' => $this->getRemark(),
            'description' => $this->getDescription(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function isCanRefund(): ?bool
    {
        return $this->canRefund;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }public function getCreatedFromIp(): ?string
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
