<?php

namespace Tourze\ProductCoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductCoreBundle\Enum\StockChange;
use Tourze\ProductCoreBundle\Repository\StockLogRepository;

#[ORM\Entity(repositoryClass: StockLogRepository::class)]
#[ORM\Table(name: 'product_stock_log', options: ['comment' => '库存日志'])]
class StockLog implements AdminArrayInterface
, \Stringable
{
    use BlameableAware;
    use SnowflakeKeyAware;
    use TimestampableAware;
    #[CreatedByColumn]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[ORM\ManyToOne(targetEntity: Sku::class, inversedBy: 'stockLogs')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Sku $sku = null;
    use StockValueAware;
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => 'SKU名'])]
    private ?string $skuName = null;
    #[ORM\Column(type: Types::STRING, length: 30, enumType: StockChange::class, options: ['comment' => '类型'])]
    private StockChange $type;
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '数量'])]
    private int $quantity = 0;
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = '';
    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;
    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

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

    public function getSkuName(): ?string
    {
        return $this->skuName;
    }

    public function setSkuName(?string $skuName): self
    {
        $this->skuName = $skuName;

        return $this;
    }

    public function renderSKU(): string
    {
        return $this->getSku() !== null ? $this->getSku()->getFullName() : '';
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

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'validStock' => $this->getValidStock(),
            'virtualStock' => $this->getVirtualStock(),
            'type' => $this->getType(),
            'quantity' => $this->getQuantity(),
            'remark' => $this->getRemark(),
        ];
    }

    public function getType(): ?StockChange
    {
        return $this->type;
    }

    public function setType(StockChange $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
}
