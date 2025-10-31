<?php

namespace Tourze\ProductCoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\CreatedFromIpAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductCoreBundle\Repository\BrandRepository;

/**
 * @see https://blog.csdn.net/zhichaosong/article/details/120316738
 */
#[ORM\Table(name: 'product_brand', options: ['comment' => '品牌管理表'])]
#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand implements \Stringable
{
    use BlameableAware;
    use CreatedFromIpAware;
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[TrackColumn]
    #[Groups(groups: ['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[Assert\NotNull(message: '有效状态不能为空')]
    private ?bool $valid = false;

    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '标题'])]
    #[Assert\NotBlank(message: '品牌名称不能为空')]
    #[Assert\Length(max: 64, maxMessage: '品牌名称长度不能超过 {{ limit }} 个字符')]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'LOGO地址'])]
    #[Assert\Length(max: 255, maxMessage: 'LOGO地址长度不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: 'LOGO地址格式不正确')]
    private ?string $logoUrl = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return "{$this->getName()}";
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): void
    {
        $this->logoUrl = $logoUrl;
    }
}
