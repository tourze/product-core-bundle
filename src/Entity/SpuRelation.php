<?php

namespace Tourze\ProductCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductCoreBundle\Repository\SpuRelationRepository;

/**
 * SPU关系描述，本质是一个三元组？记录主谓宾关系
 */
#[ORM\Table(name: 'product_spu_relation', options: ['comment' => '产品SPU关系表'])]
#[ORM\Entity(repositoryClass: SpuRelationRepository::class)]
class SpuRelation implements \Stringable
{
    use BlameableAware;
    use TimestampableAware;
    use SnowflakeKeyAware;
    #[CreatedByColumn]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Spu $spu1 = null;
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Spu $spu2 = null;
    #[ORM\Column(length: 30, options: ['comment' => '关系类型'])]
    private ?string $relation = null;


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

    public function getSpu1(): ?Spu
    {
        return $this->spu1;
    }

    public function setSpu1(?Spu $spu1): self
    {
        $this->spu1 = $spu1;

        return $this;
    }

    public function getSpu2(): ?Spu
    {
        return $this->spu2;
    }

    public function setSpu2(?Spu $spu2): self
    {
        $this->spu2 = $spu2;

        return $this;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
}
