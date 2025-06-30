<?php

namespace Tourze\ProductCoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnumExtra\Itemable;
use Tourze\ProductCoreBundle\Repository\TagRepository;

#[ORM\Table(name: 'product_tag', options: ['comment' => '产品标签'])]
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag implements \Stringable, Itemable
{
        use BlameableAware;
    use TimestampableAware;


    #[Groups(groups: ['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '标签ID'])]
    private ?int $id = 0;
    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '目录'])]
    private ?string $category = null;
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
        if ($this->getId() === null || $this->getId() === 0) {
            return '';
        }

        if (!empty($this->getCategory())) {
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
