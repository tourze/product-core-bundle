<?php

namespace Tourze\ProductCoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductCoreBundle\Repository\SkuAttributeRepository;

#[ORM\Table(name: 'product_sku_attribute', options: ['comment' => '产品SKU属性'])]
#[ORM\Entity(repositoryClass: SkuAttributeRepository::class)]
class SkuAttribute implements \Stringable, AdminArrayInterface
{
        use BlameableAware;
    use TimestampableAware;

    #[Groups(groups: ['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;


    #[Groups(groups: ['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;
    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Sku::class, cascade: ['persist'], inversedBy: 'attributes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Sku $sku = null;
    #[ORM\Column(type: Types::STRING, length: 30, options: ['comment' => '属性名'])]
    private ?string $name = null;
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '属性值'])]
    private ?string $value = null;
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '允许定制'])]
    private bool $allowCustomized = false;
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

        return "{$this->getName()}:{$this->getValue()}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return strval($this->name);
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): string
    {
        return strval($this->value);
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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

    public function getSku(): ?Sku
    {
        return $this->sku;
    }

    public function setSku(?Sku $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function setAllowCustomized(bool $allowCustomized): self
    {
        $this->allowCustomized = $allowCustomized;

        return $this;
    }

    public function retrieveSkuArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'remark' => $this->getRemark(),
            'allowCustomized' => $this->isAllowCustomized(),
        ];
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

    public function isAllowCustomized(): bool
    {
        return boolval($this->allowCustomized);
    }

    public function retrieveSpuArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
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
