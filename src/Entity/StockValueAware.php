<?php

namespace ProductCoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * 一些常规的库存字段信息.
 */
trait StockValueAware
{
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '可用库存', 'default' => 0])]
    private ?int $validStock = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '已售库存', 'default' => 0])]
    private ?int $soldStock = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '锁定库存', 'default' => 0])]
    private ?int $lockStock = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '虚拟库存', 'default' => 0])]
    private ?int $virtualStock = 0;

    public function getValidStock(): ?int
    {
        return $this->validStock;
    }

    public function setValidStock(int $validStock): self
    {
        $this->validStock = $validStock;

        return $this;
    }

    public function getSoldStock(): ?int
    {
        return $this->soldStock;
    }

    public function setSoldStock(int $soldStock): self
    {
        $this->soldStock = $soldStock;

        return $this;
    }

    public function getLockStock(): ?int
    {
        return $this->lockStock;
    }

    public function setLockStock(int $lockStock): self
    {
        $this->lockStock = $lockStock;

        return $this;
    }

    public function getVirtualStock(): ?int
    {
        return $this->virtualStock;
    }

    public function setVirtualStock(?int $virtualStock): void
    {
        $this->virtualStock = $virtualStock;
    }
}
