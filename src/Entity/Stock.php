<?php

namespace Tourze\ProductCoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrinePrecisionBundle\Attribute\PrecisionColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductCoreBundle\Enum\StockState;
use Tourze\ProductCoreBundle\Repository\StockRepository;

/**
 * 这个表可能很巨大
 * 严格来讲，每个库存也会有一些关键属性，例如标记实际产地、品牌等信息
 *
 * @see https://www.woshipm.com/pd/615772.html
 * @see https://vip.kingdee.com/knowledge/specialDetail/188692783110897152?productLineId=2&category=229269976182821632&id=263954613324586496
 */
#[ORM\Entity(repositoryClass: StockRepository::class)]
#[ORM\Table(name: 'product_stock', options: ['comment' => '产品库存详情'])]
class Stock implements \Stringable
{
        use BlameableAware;
    use TimestampableAware;

    #[Groups(groups: ['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;


    #[Groups(groups: ['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[ORM\ManyToOne(targetEntity: Sku::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sku $sku = null;
    #[ORM\Column(type: Types::STRING, length: 10, nullable: true, options: ['default' => 'CNY', 'comment' => '成本币种'])]
    private ?string $costCurrency = null;
    #[PrecisionColumn]
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2, nullable: true, options: ['comment' => '成本价格'])]
    private ?string $costPrice = null;
    #[ORM\Column(type: Types::STRING, length: 40, enumType: StockState::class, options: ['comment' => '状态'])]
    private StockState $state;
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;
    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 1, 'comment' => '乐观锁版本号'])]
    private ?int $lockVersion = null;public function __toString(): string
    {
        if ($this->getId() === null || $this->getId() === 0) {
            return '';
        }

        return "{$this->getSku()} | {$this->getId()}";
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getState(): StockState
    {
        return $this->state;
    }

    public function setState(StockState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCostPrice(): ?string
    {
        return $this->costPrice;
    }

    public function setCostPrice(?string $costPrice): self
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    public function getCostCurrency(): ?string
    {
        return $this->costCurrency;
    }

    public function setCostCurrency(?string $costCurrency): self
    {
        $this->costCurrency = $costCurrency;

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

    public function getLockVersion(): ?int
    {
        return $this->lockVersion;
    }

    public function setLockVersion(?int $lockVersion): self
    {
        $this->lockVersion = $lockVersion;

        return $this;
    }

    public function renderSkuColumn(): string
    {
        return $this->getSku() !== null ? $this->getSku()->getFullName() : '';
    }
}
