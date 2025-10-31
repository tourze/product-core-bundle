<?php

namespace Tourze\ProductCoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Traits\CreatedFromIpAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductCoreBundle\Enum\PackageType;
use Tourze\ProductCoreBundle\Repository\SkuPackageRepository;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Table(name: 'product_sku_package', options: ['comment' => '打包属性'])]
#[ORM\Entity(repositoryClass: SkuPackageRepository::class)]
class SkuPackage implements \Stringable, AdminArrayInterface
{
    use BlameableAware;
    use TimestampableAware;
    use SnowflakeKeyAware;
    use CreatedFromIpAware;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Sku::class, inversedBy: 'packages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sku $sku = null;

    #[Assert\Choice(callback: [PackageType::class, 'cases'], message: '请选择正确的打包类型')]
    #[ORM\Column(type: Types::STRING, length: 30, enumType: PackageType::class, options: ['comment' => '打包类型'])]
    private ?PackageType $type = null;

    #[Assert\NotBlank(message: '属性值不能为空')]
    #[Assert\Length(max: 64, maxMessage: '属性值不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '属性值'])]
    private ?string $value = null;

    #[Assert\Positive(message: '数量必须为正数')]
    #[ORM\Column(nullable: false, options: ['comment' => '数量', 'default' => 1])]
    private int $quantity = 0;

    #[Assert\Length(max: 100, maxMessage: '备注不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    public function __toString(): string
    {
        if (null === $this->getId() || '' === $this->getId()) {
            return '';
        }

        $rs = "{$this->getType()?->getLabel()} {$this->getValue()}";
        if (null !== $this->getRemark() && '' !== $this->getRemark()) {
            $rs = "{$rs}({$this->getRemark()})";
        }

        return $rs;
    }

    public function getType(): ?PackageType
    {
        return $this->type;
    }

    public function setType(PackageType $type): void
    {
        $this->type = $type;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getSku(): ?Sku
    {
        return $this->sku;
    }

    public function setSku(?Sku $sku): void
    {
        $this->sku = $sku;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveSpuArray(): array
    {
        return [
            'id' => $this->getId(),
            'value' => $this->getValue(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'value' => $this->getValue(),
            'type' => $this->getType(),
            'quantity' => $this->getQuantity(),
            'remark' => $this->getRemark(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity ?? 0;
    }
}
