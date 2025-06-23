<?php

namespace ProductCoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * 商品申请表
 */
#[ORM\Table(name: 'ims_goods_subscribe', options: ['comment' => '商品申请表'])]
#[ORM\UniqueConstraint(name: 'goods_subscribe_idx_unique', columns: ['goods_id', 'member_id'])]
#[ORM\Entity]
class SpuSubscribe implements \Stringable
{
    use TimestampableAware;

    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;


    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private int $goodsId;
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['comment' => '用户'])]
    private int $memberId;
    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => '0', 'comment' => '状态'])]
    private bool $status = false;

    public function getId(): ?int
    {
        return $this->id;
    }public function getGoodsId(): int
    {
        return $this->goodsId;
    }

    public function setGoodsId(int $goodsId): void
    {
        $this->goodsId = $goodsId;
    }

    public function getMemberId(): int
    {
        return $this->memberId;
    }

    public function setMemberId(int $memberId): void
    {
        $this->memberId = $memberId;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
}
