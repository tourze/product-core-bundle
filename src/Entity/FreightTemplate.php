<?php

namespace ProductBundle\Entity;

use AppBundle\Service\CurrencyManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DoctrineEnhanceBundle\Traits\SortableTrait;
use ProductBundle\Enum\DeliveryType;
use ProductBundle\Enum\FreightValuationType;
use ProductBundle\Repository\FreightTemplateRepository;
use StoreBundle\Entity\Store;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrinePrecisionBundle\Attribute\PrecisionColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Field\SelectField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

/**
 * @see https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent/API/freight/update_freight_template.html
 */
#[AsPermission(title: '运费模板')]
#[Listable]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: FreightTemplateRepository::class)]
#[ORM\Table(name: 'product_freight_template', options: ['comment' => '运费模板'])]
class FreightTemplate implements \Stringable, ApiArrayInterface, AdminArrayInterface
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
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]#[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'recursive_view', 'api_tree'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;
    #[CreatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;
    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[Groups(['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;
    use SortableTrait;
    #[Groups(['admin_curd', 'restful_read'])]
    #[ListColumn]
    #[FormField]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '名称'])]
    private string $name;
    #[Groups(['admin_curd', 'restful_read'])]
    #[ListColumn]
    #[FormField(span: 6)]
    #[TrackColumn]
    #[ORM\Column(length: 40, enumType: DeliveryType::class, options: ['comment' => '配送方式'])]
    private ?DeliveryType $deliveryType = null;
    #[Groups(['admin_curd'])]
    #[ListColumn]
    #[FormField(span: 6)]
    #[TrackColumn]
    #[ORM\Column(length: 40, enumType: FreightValuationType::class, options: ['comment' => '计费方式'])]
    private ?FreightValuationType $valuationType = null;
    #[Groups(['admin_curd'])]
    #[SelectField(targetEntity: CurrencyManager::class)]
    #[TrackColumn]
    #[ListColumn]
    #[FormField(span: 6)]
    #[Filterable]
    #[ORM\Column(type: Types::STRING, length: 10, options: ['default' => 'CNY', 'comment' => '币种'])]
    private ?string $currency = null;
    #[PrecisionColumn]
    #[Groups(['admin_curd'])]
    #[ListColumn]
    #[FormField(span: 6)]
    #[TrackColumn]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '费用'])]
    private ?string $fee = null;
    #[Groups(['admin_curd'])]
    #[ListColumn(title: '关联SPU')]
    #[FormField(title: '关联SPU', allowRemote: false, remoteLimit: 99999)]
    #[ORM\ManyToMany(targetEntity: Spu::class, mappedBy: 'freightTemplates', fetch: 'EXTRA_LAZY')]
    private Collection $spus;
    #[Groups(['admin_curd'])]
    #[ListColumn(title: '关联门店')]
    #[FormField(title: '关联门店')]
    #[ORM\ManyToMany(targetEntity: Store::class, fetch: 'EXTRA_LAZY')]
    private Collection $stores;

    public function __construct()
    {
        $this->spus = new ArrayCollection();
        $this->stores = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getName()}";
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @return Collection<int, Spu>
     */
    public function getSpus(): Collection
    {
        return $this->spus;
    }

    public function addSpu(Spu $spu): self
    {
        if (!$this->spus->contains($spu)) {
            $this->spus->add($spu);
            $spu->addFreightTemplate($this);
        }

        return $this;
    }

    public function removeSpu(Spu $spu): self
    {
        if ($this->spus->removeElement($spu)) {
            $spu->removeFreightTemplate($this);
        }

        return $this;
    }

    public function addStore(Store $store): self
    {
        if (!$this->stores->contains($store)) {
            $this->stores->add($store);
        }

        return $this;
    }

    public function removeStore(Store $store): self
    {
        $this->stores->removeElement($store);

        return $this;
    }

    public function retrieveApiArray(): array
    {
        $stores = [];
        foreach ($this->getStores() as $store) {
            $stores[] = $store->retrieveApiArray();
        }

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'deliveryType' => $this->getDeliveryType()?->value,
            'stores' => $stores,
            'currency' => $this->getCurrency(),
            'fee' => $this->getFee(),
        ];
    }

    /**
     * @return Collection<int, Store>
     */
    public function getStores(): Collection
    {
        return $this->stores;
    }

    public function getDeliveryType(): ?DeliveryType
    {
        return $this->deliveryType;
    }

    public function setDeliveryType(DeliveryType $deliveryType): self
    {
        $this->deliveryType = $deliveryType;

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

    public function getFee(): ?string
    {
        return $this->fee;
    }

    public function setFee(?string $fee): self
    {
        $this->fee = $fee;

        return $this;
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            ...$this->retrieveSortableArray(),
            'name' => $this->getName(),
            'deliveryType' => $this->getDeliveryType(),
            'currency' => $this->getCurrency(),
            'fee' => $this->getFee(),
            'valuationType' => $this->getValuationType(),
        ];
    }public function getValuationType(): ?FreightValuationType
    {
        return $this->valuationType;
    }

    public function setValuationType(FreightValuationType $valuationType): self
    {
        $this->valuationType = $valuationType;

        return $this;
    }
}
