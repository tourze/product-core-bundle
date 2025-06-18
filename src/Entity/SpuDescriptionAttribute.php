<?php

namespace ProductBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ProductBundle\Repository\SpuDescriptionAttributeRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: 'spu描述属性')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Table(name: 'product_sku_description_attribute', options: ['comment' => '产品SPU描述属性'])]
#[ORM\Entity(repositoryClass: SpuDescriptionAttributeRepository::class)]
class SpuDescriptionAttribute implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]#[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]#[CreatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;
    #[TrackColumn]
    #[FormField(span: 6)]
    #[Filterable]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 30, options: ['comment' => '标题'])]
    private ?string $name = null;
    #[TrackColumn]
    #[FormField(span: 16)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '内容'])]
    private ?string $value = null;
    #[TrackColumn]
    #[Ignore]
    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'descriptionAttribute')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Spu $spu;

    public function __toString(): string
    {
        if (!$this->getId()) {
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

    public function getSpu(): Spu
    {
        return $this->spu;
    }

    public function setSpu(Spu $spu): self
    {
        $this->spu = $spu;

        return $this;
    }

    public function retrieveSpuArray(): array
    {
        return [
            'name' => $this->getName(),
            'value' => $this->getValue(),
        ];
    }

    public function retrieveCheckoutArray(): array
    {
        return [
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
    }}
