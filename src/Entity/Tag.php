<?php

namespace ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ProductBundle\Repository\TagRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\EnumExtra\Itemable;

#[AsPermission(title: '标签')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Table(name: 'product_tag', options: ['comment' => '产品标签'])]
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag implements \Stringable, Itemable
{
    use TimestampableAware;
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
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '标签ID'])]
    private ?int $id = 0;
    #[IndexColumn]
    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '目录'])]
    private ?string $category = null;
    #[IndexColumn]
    #[FormField]
    #[Filterable]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 30, unique: true, options: ['comment' => '标签名'])]
    private ?string $name = null;
    /**
     * @var Collection<Spu>
     */
    #[Ignore]
    #[ORM\ManyToMany(targetEntity: Spu::class, inversedBy: 'tags', fetch: 'EXTRA_LAZY')]
    private Collection $spus;
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '文字颜色'])]
    private ?string $textColor = null;
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '背景颜色'])]
    private ?string $bgColor = null;
    #[FormField]
    #[ListColumn]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    public function __construct()
    {
        $this->spus = new ArrayCollection();
    }public function getCreatedBy(): ?string
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

    public function addSpu(Spu $spu): self
    {
        if (!$this->spus->contains($spu)) {
            $this->spus[] = $spu;
        }

        return $this;
    }

    public function removeSpu(Spu $spu): self
    {
        $this->spus->removeElement($spu);

        return $this;
    }

    #[ListColumn(title: 'SPU数量')]
    public function getSpuCount(): int
    {
        return $this->getSpus()->count();
    }

    /**
     * @return Collection<Spu>
     */
    public function getSpus(): Collection
    {
        return $this->spus;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(?string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getBgColor(): ?string
    {
        return $this->bgColor;
    }

    public function setBgColor(?string $bgColor): self
    {
        $this->bgColor = $bgColor;

        return $this;
    }

    public function toSelectItem(): array
    {
        return [
            'label' => $this->__toString(),
            'text' => $this->__toString(),
            'value' => $this->getId(),
        ];
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        if ($this->getCategory()) {
            return "[{$this->getCategory()}]{$this->getName()}";
        }

        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function retrieveSpuArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }
}
