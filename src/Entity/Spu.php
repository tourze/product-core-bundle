<?php

namespace Tourze\ProductCoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\DoctrineHelper\SortableTrait;
use Tourze\DoctrineIpBundle\Traits\CreatedFromIpAware;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnumExtra\Itemable;
use Tourze\ProductAttributeBundle\Entity\SpuAttribute;
use Tourze\ProductCoreBundle\Enum\SpuState;
use Tourze\ProductCoreBundle\Repository\SpuRepository;
use Tourze\ResourceManageBundle\Model\ResourceIdentity;
use Tourze\TagManageBundle\Entity\Tag;

/**
 * @implements AdminArrayInterface<string, mixed>
 * @see https://www.duosku.com/tmallyunying/4.html
 * @see https://blog.csdn.net/zhichaosong/article/details/120316738 这种设计很适合多商户体系
 */
#[ORM\Table(name: 'product_spu', options: ['comment' => '产品SPU'])]
#[ORM\Entity(repositoryClass: SpuRepository::class)]
class Spu implements \Stringable, Itemable, AdminArrayInterface, ResourceIdentity, \Tourze\ProductServiceContracts\SPU
{
    use BlameableAware;
    use TimestampableAware;
    use SortableTrait;
    use CreatedFromIpAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'SpuID'])]
    private int $id = 0;

    #[TrackColumn]
    #[SnowflakeColumn(prefix: 'SPU')]
    #[Assert\Length(max: 40, maxMessage: 'GTIN 不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 40, unique: true, nullable: true, options: ['comment' => '唯一编码'])]
    private ?string $gtin = '';

    #[Assert\NotBlank(message: '标题不能为空')]
    #[Assert\Length(max: 120, maxMessage: '标题不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '标题'])]
    private string $title = '';

    #[Assert\Length(max: 60, maxMessage: '类型不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 60, nullable: true, options: ['default' => 'normal', 'comment' => '类型'])]
    private ?string $type = null;

    #[Groups(groups: ['admin_curd'])]
    #[ORM\ManyToOne(targetEntity: Brand::class)]
    private ?Brand $brand = null;

    #[Assert\Length(max: 1024, maxMessage: '副标题不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 1024, nullable: true, options: ['comment' => '副标题'])]
    private ?string $subtitle = '';

    /**
     * @var Collection<int, Sku>
     */
    #[ORM\OneToMany(targetEntity: Sku::class, mappedBy: 'spu', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $skus;

    /**
     * @var Collection<int, Catalog>
     */
    #[ORM\ManyToMany(targetEntity: Catalog::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'spu_catalogs')]
    private Collection $categories;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'spu_tags')]
    private Collection $tags;

    #[Assert\Choice(callback: [SpuState::class, 'cases'], message: '请选择正确的状态')]
    #[ORM\Column(type: Types::STRING, length: 40, nullable: true, enumType: SpuState::class, options: ['comment' => '状态'])]
    private ?SpuState $state;

    #[Assert\Length(max: 1000, maxMessage: '主图不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '主图'])]
    private ?string $mainPic = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array', message: '轮播图必须为数组类型')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '轮播图'])]
    private ?array $thumbs = [];

    /**
     * 有些地方也叫核心属性.
     *
     * @var Collection<int, SpuAttribute>
     */
    #[ORM\OneToMany(targetEntity: SpuAttribute::class, mappedBy: 'spu', cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $attributes;

    #[Assert\Length(max: 65535, maxMessage: '描述不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $content = null;

    #[Assert\Length(max: 65535, maxMessage: '备注不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[Groups(groups: ['admin_curd', 'restful_read'])]
    #[Assert\Type(type: 'bool', message: '上架必须为布尔值')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否上架'])]
    private ?bool $valid = false;

    public function __construct()
    {
        $this->skus = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function addSku(Sku $sku): self
    {
        if (!$this->skus->contains($sku)) {
            $this->skus->add($sku);
            $sku->setSpu($this);
        }

        return $this;
    }

    public function removeSku(Sku $sku): self
    {
        if ($this->skus->removeElement($sku)) {
            // set the owning side to null (unless already changed)
            if ($sku->getSpu() === $this) {
                $sku->setSpu(null);
            }
        }

        return $this;
    }

    public function addCategory(Catalog $catalog): self
    {
        if (!$this->categories->contains($catalog)) {
            $this->categories->add($catalog);
        }

        return $this;
    }

    public function removeCategory(Catalog $catalog): self
    {
        $this->categories->removeElement($catalog);

        return $this;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getState(): ?SpuState
    {
        return $this->state;
    }

    public function setState(?SpuState $state): void
    {
        $this->state = $state;
    }

    public function addAttribute(SpuAttribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            $attribute->setSpu($this);
        }

        return $this;
    }

    public function removeAttribute(SpuAttribute $attribute): self
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getSpu() === $this) {
                $attribute->setSpu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sku>
     */
    public function getSkus(): Collection
    {
        return $this->skus;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * @return array<string, mixed>
     */
    public function toSelectItem(): array
    {
        return [
            'label' => $this->getTitle(),
            'text' => $this->getTitle(),
            'value' => $this->getId(),
        ];
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        $categories = [];
        foreach ($this->getCategories() as $catalog) {
            $categories[] = [
                'id' => $catalog->getId(),
                'name' => $catalog->getName(),
            ];
        }

        $tags = [];
        foreach ($this->getTags() as $tag) {
            $tags[] = [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            ];
        }

        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'gtin' => $this->getGtin(),
            'title' => $this->getTitle(),
            'type' => $this->getType(),
            'mainPic' => $this->getMainPic(),
            'valid' => $this->isValid(),
            ...$this->retrieveSortableArray(),
            'brand' => $this->getBrand(),
            'subtitle' => $this->getSubtitle(),
            'thumbs' => $this->getThumbs(),
            'content' => $this->getContent(),
            'categories' => $categories,
            'tags' => $tags,
        ];
    }

    /**
     * @return Collection<int, Catalog>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getGtin(): ?string
    {
        return $this->gtin;
    }

    public function setGtin(?string $gtin): void
    {
        $this->gtin = $gtin;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getMainPic(): ?string
    {
        return $this->mainPic;
    }

    public function setMainPic(?string $mainPic): void
    {
        $this->mainPic = $mainPic;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): void
    {
        $this->brand = $brand;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return array<mixed>|null
     */
    public function getThumbs(): ?array
    {
        return $this->thumbs;
    }

    /**
     * @param array<mixed>|null $thumbs
     */
    public function setThumbs(?array $thumbs): void
    {
        $this->thumbs = $thumbs;
    }

    /**
     * @return array<array{url: string, sort: int}>
     */
    public function getImages(): array
    {
        if (null === $this->thumbs || [] === $this->thumbs) {
            return [];
        }

        $images = $this->processThumbsArray();
        usort($images, fn (array $a, array $b) => $a['sort'] <=> $b['sort']);

        return $images;
    }

    /**
     * @return array<array{url: string, sort: int}>
     */
    private function processThumbsArray(): array
    {
        $images = [];
        if (!is_array($this->thumbs)) {
            return $images;
        }

        foreach ($this->thumbs as $index => $thumb) {
            $image = $this->processThumbItem($thumb, $index);
            // Only include images with valid URLs
            if ('' !== $image['url']) {
                $images[] = $image;
            }
        }

        return $images;
    }

    /**
     * @return array{url: string, sort: int}
     */
    private function processThumbItem(mixed $thumb, int $index): array
    {
        if (is_array($thumb) && isset($thumb['url'])) {
            $url = $thumb['url'];
            $thumbUrl = is_string($url) ? $url : (is_scalar($url) ? (string) $url : '');

            return [
                'url' => $thumbUrl,
                'sort' => is_int($thumb['sort'] ?? null) ? $thumb['sort'] : $index,
            ];
        }

        if (is_string($thumb)) {
            return [
                'url' => $thumb,
                'sort' => $index,
            ];
        }

        return ['url' => '', 'sort' => $index];
    }

    /**
     * @param array<array{url: string, sort: int}> $images
     */
    public function setImages(array $images): void
    {
        $this->thumbs = $images;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return Collection<int, SpuAttribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function getResourceId(): string
    {
        return (string) $this->getId();
    }

    public function getResourceLabel(): string
    {
        return $this->getTitle();
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveSpuArray(): array
    {
        $skus = [];
        foreach ($this->getSkus() as $sku) {
            $skus[] = $sku->retrieveSkuArray();
        }

        $attributes = [];
        foreach ($this->getAttributes() as $attribute) {
            $attributes[] = $attribute->retrieveSpuArray();
        }

        return [
            'id' => $this->getId(),
            'supplier' => null,
            'gtin' => $this->getGtin(),
            'title' => $this->getTitle(),
            'subtitle' => $this->getSubtitle(),
            'type' => $this->getType(),
            'skus' => $skus,
            'mainPic' => $this->getMainPic(),
            'thumbs' => $this->getThumbs(),
            'attributes' => $attributes,
            'content' => $this->getContent(),
            'mainThumb' => $this->getMainThumb(),
        ];
    }

    /**
     * 获取主缩略图
     */
    private function getMainThumb(): string
    {
        $mainPic = $this->getMainPic();
        if (null !== $mainPic && '' !== $mainPic) {
            return $mainPic;
        }

        $thumbs = $this->getThumbs();
        if (null === $thumbs || [] === $thumbs) {
            return '';
        }

        $firstThumb = $thumbs[0] ?? null;
        if (is_array($firstThumb) && isset($firstThumb['url'])) {
            $url = $firstThumb['url'];

            return is_string($url) ? $url : (is_scalar($url) ? (string) $url : '');
        }

        return '';
    }
}
