<?php

namespace ProductBundle\Entity;

use AntdCpBundle\Builder\Field\BraftEditor;
use AntdCpBundle\Builder\Field\DynamicFieldSet;
use AntdCpBundle\Builder\Field\TreeSelectField;
use App\Kernel;
use AppBundle\Entity\Supplier;
use AppBundle\Service\CurrencyManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DoctrineEnhanceBundle\Traits\SortableTrait;
use ProductBundle\Enum\SpuState;
use ProductBundle\ProductTypeFetcher;
use ProductBundle\Repository\SpuRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Event\BeforeCreate;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EasyAdmin\Attribute\Field\RichTextField;
use Tourze\EasyAdmin\Attribute\Field\SelectField;
use Tourze\EnumExtra\Itemable;
use Tourze\ResourceManageBundle\Model\ResourceIdentity;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Json\Json;

/**
 * @see https://www.duosku.com/tmallyunying/4.html
 * @see https://blog.csdn.net/zhichaosong/article/details/120316738 这种设计很适合多商户体系
 */
#[ORM\Table(name: 'product_spu', options: ['comment' => '产品SPU'])]
#[ORM\Entity(repositoryClass: SpuRepository::class)]
class Spu implements \Stringable, Itemable, AdminArrayInterface, ResourceIdentity
{
        use BlameableAware;
    use TimestampableAware;


    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'SpuID'])]
    private ?int $id = 0;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Supplier $supplier = null;
    use SortableTrait;
    /**
     * 全球唯一编码，可以是UPC、EAN、69码等等.
     */
    #[TrackColumn]
    #[SnowflakeColumn(prefix: 'SPU')]
    #[ORM\Column(type: Types::STRING, length: 40, unique: true, nullable: true, options: ['comment' => '唯一编码'])]
    private ?string $gtin = '';
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '标题'])]
    private string $title = '';
    #[SelectField(targetEntity: ProductTypeFetcher::class)]
    #[ORM\Column(type: Types::STRING, length: 60, nullable: true, options: ['default' => 'normal', 'comment' => '类型'])]
    private ?string $type = null;
    #[Groups(['admin_curd'])]
    #[ORM\ManyToOne(targetEntity: Brand::class)]
    private ?Brand $brand = null;
    #[ORM\Column(type: Types::STRING, length: 1024, nullable: true, options: ['comment' => '副标题'])]
    private ?string $subtitle = '';
    /**
     * @var Collection<Sku>
     */
    #[ORM\OneToMany(mappedBy: 'spu', targetEntity: Sku::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $skus;
    /**
     * @TreeSelectField(treeModel=Category::class, multiple=true)
     *
     * @var Collection<Category>
     */

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'spus', fetch: 'EXTRA_LAZY')]
    private Collection $categories;
    /**
     * @var Collection<Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'spus', fetch: 'EXTRA_LAZY')]
    private Collection $tags;
    #[ORM\Column(type: Types::STRING, length: 40, nullable: true, enumType: SpuState::class, options: ['comment' => '状态'])]
    private ?SpuState $state;
    #[ImagePickerField]
    #[PictureColumn]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '主图'])]
    private ?string $mainPic = null;
    #[ImagePickerField(limit: 9)]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '轮播图'])]
    private ?array $thumbs = [];
    /**
     * 有些地方也叫核心属性.
     *
     * @DynamicFieldSet
     *
     * @var Collection<SpuAttribute>
     */
    #[ORM\OneToMany(mappedBy: 'spu', targetEntity: SpuAttribute::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $attributes;
    /**
     * @DynamicFieldSet
     *
     * @var Collection<SpuDescriptionAttribute>
     */
    #[ORM\OneToMany(mappedBy: 'spu', targetEntity: SpuDescriptionAttribute::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $descriptionAttribute;
    /**
     * @BraftEditor
     */
    #[RichTextField]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $content = null;
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;
    /**
     * @DynamicFieldSet()
     *
     * @var Collection<SpuLimitRule>
     */
    #[ORM\OneToMany(mappedBy: 'spu', targetEntity: SpuLimitRule::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $limitRules;
    #[SelectField(targetEntity: 'product.tag.fetcher', mode: 'multiple')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '显示标签'])]
    private ?array $showTags = [];
    #[Ignore]
    #[ORM\ManyToMany(targetEntity: FreightTemplate::class, inversedBy: 'spus', fetch: 'EXTRA_LAZY')]
    private Collection $freightTemplates;
    #[Groups(['admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '上架'])]
    private ?bool $valid = false;
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '审核状态', 'default' => 1])]
    private ?bool $audited = true;
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '自动发布时间'])]
    private ?\DateTimeInterface $autoReleaseTime = null;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '自动下架时间'])]
    private ?\DateTimeInterface $autoTakeDownTime = null;
    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;
    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->skus = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->limitRules = new ArrayCollection();
        $this->descriptionAttribute = new ArrayCollection();
        $this->freightTemplates = new ArrayCollection();
    }

    public function __toString(): string
    {
        return "#{$this->getId()} {$this->getTitle()}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function addSku(Sku $sku): self
    {
        if (!$this->skus->contains($sku)) {
            $this->skus[] = $sku;
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

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addSpu($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeSpu($this);
        }

        return $this;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addSpu($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeSpu($this);
        }

        return $this;
    }

    public function getState(): ?SpuState
    {
        return $this->state;
    }

    public function setState(?SpuState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function addAttribute(SpuAttribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
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
     * 获取在售库存.
     */
    public function getOnSaleStock(): int
    {
        if (isset($_ENV['FIXED_PRODUCT_STOCK_NUMBER'])) {
            return intval($_ENV['FIXED_PRODUCT_STOCK_NUMBER']);
        }

        $r = 0;
        foreach ($this->getSkus() as $sku) {
            $r += $sku->getValidStock();
        }

        return $r;
    }

    /**
     * @return Collection<Sku>
     */
    public function getSkus(): Collection
    {
        return $this->skus;
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

    public function toSelectItem(): array
    {
        return [
            'label' => $this->getTitle(),
            'text' => $this->getTitle(),
            'value' => $this->getId(),
        ];
    }

    public function addLimitRule(SpuLimitRule $limitRule): self
    {
        if (!$this->limitRules->contains($limitRule)) {
            $this->limitRules[] = $limitRule;
            $limitRule->setSpu($this);
        }

        return $this;
    }

    public function removeLimitRule(SpuLimitRule $limitRule): self
    {
        if ($this->limitRules->removeElement($limitRule)) {
            // set the owning side to null (unless already changed)
            if ($limitRule->getSpu() === $this) {
                $limitRule->setSpu(null);
            }
        }

        return $this;
    }

    public function removeDescriptionAttribute(SpuDescriptionAttribute $descriptionAttribute): self
    {
        if ($this->descriptionAttribute->removeElement($descriptionAttribute)) {
            // set the owning side to null (unless already changed)
            if ($descriptionAttribute->getSpu() === $this) {
                $descriptionAttribute->setSpu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FreightTemplate>
     */
    public function getFreightTemplates(): Collection
    {
        return $this->freightTemplates;
    }

    public function addFreightTemplate(FreightTemplate $freightTemplate): self
    {
        if (!$this->freightTemplates->contains($freightTemplate)) {
            $this->freightTemplates->add($freightTemplate);
        }

        return $this;
    }

    public function removeFreightTemplate(FreightTemplate $freightTemplate): self
    {
        $this->freightTemplates->removeElement($freightTemplate);

        return $this;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function setAudited(?bool $audited): self
    {
        $this->audited = $audited;

        return $this;
    }

    #[BeforeCreate]
    public function autoAssignSupplier(Security $security): void
    {
        $user = $security->getUser();
        if (!$user->getSupplier()) {
            return;
        }
        if (!$this->getSupplier()) {
            $this->setSupplier($user->getSupplier());
        }
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function retrieveAdminArray(): array
    {
        $categories = [];
        foreach ($this->getCategories() as $category) {
            $categories[] = $category->retrieveAdminArray();
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
            'audited' => $this->isAudited(),
            ...$this->retrieveSortableArray(),
            'brand' => $this->getBrand(),
            'subtitle' => $this->getSubtitle(),
            'thumbs' => $this->getThumbs(),
            'content' => $this->getContent(),
            'showTags' => $this->getShowTags(),
            'autoReleaseTime' => $this->getAutoReleaseTime(),
            'autoTakeDownTime' => $this->getAutoTakeDownTime(),
            'categories' => $categories,
        ];
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }public function getGtin(): ?string
    {
        return $this->gtin;
    }

    public function setGtin(?string $gtin): self
    {
        $this->gtin = $gtin;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMainPic(): ?string
    {
        return $this->mainPic;
    }

    public function setMainPic(?string $mainPic): self
    {
        $this->mainPic = $mainPic;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function isAudited(): ?bool
    {
        return $this->audited;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getThumbs(): ?array
    {
        return $this->thumbs;
    }

    public function setThumbs(?array $thumbs): self
    {
        $this->thumbs = $thumbs;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getShowTags(): ?array
    {
        return $this->showTags;
    }

    public function setShowTags(?array $showTags): self
    {
        $this->showTags = $showTags;

        return $this;
    }

    public function getAutoReleaseTime(): ?\DateTimeInterface
    {
        return $this->autoReleaseTime;
    }

    public function setAutoReleaseTime(?\DateTimeInterface $autoReleaseTime): void
    {
        $this->autoReleaseTime = $autoReleaseTime;
    }

    public function getAutoTakeDownTime(): ?\DateTimeInterface
    {
        return $this->autoTakeDownTime;
    }

    public function setAutoTakeDownTime(?\DateTimeInterface $autoTakeDownTime): void
    {
        $this->autoTakeDownTime = $autoTakeDownTime;
    }

    public function retrieveSpuArray(): array
    {
        $skus = [];
        foreach ($this->getSkus() as $sku) {
            $skus[] = $sku->retrieveSpuArray();
        }

        $tags = [];
        foreach ($this->getTags() as $tag) {
            $tags[] = $tag->retrieveSpuArray();
        }

        $attributes = [];
        foreach ($this->getAttributes() as $attribute) {
            $attributes[] = $attribute->retrieveSpuArray();
        }

        $descriptionAttribute = [];
        foreach ($this->getDescriptionAttribute() as $item) {
            $descriptionAttribute[] = $item->retrieveSpuArray();
        }

        return [
            'id' => $this->getId(),
            'supplier' => $this->getSupplier()?->retrievePlainArray(),
            'gtin' => $this->getGtin(),
            'title' => $this->getTitle(),
            'subtitle' => $this->getSubtitle(),
            'type' => $this->getType(),
            'skus' => $skus,
            'tags' => $tags,
            'mainPic' => $this->getMainPic(),
            'thumbs' => $this->getThumbs(),
            'attributes' => $attributes,
            'descriptionAttribute' => $descriptionAttribute,
            'content' => $this->getContent(),
            'mainThumb' => $this->getMainThumb(),
            'limitConfig' => $this->getLimitConfig(),
            'salePrices' => $this->getSalePrices(),
            'displaySalePrice' => $this->getDisplaySalePrice(),
            'displayTaxPrice' => $this->getDisplayTaxPrice(),
        ];
    }

    /**
     * @return Collection<Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @return Collection<int, SpuAttribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * @return Collection<int, SpuDescriptionAttribute>
     */
    public function getDescriptionAttribute(): Collection
    {
        if ($this->descriptionAttribute->isEmpty() && isset($_ENV['PRODUCT_SPU_DEFAULT_DESCRIPTION_ATTRIBUTES'])) {
            $json = Json::decode($_ENV['PRODUCT_SPU_DEFAULT_DESCRIPTION_ATTRIBUTES']);
            foreach ($json as $item) {
                $attribute = new SpuDescriptionAttribute();
                $attribute->setName(ArrayHelper::getValue($item, 'name'));
                $attribute->setValue(ArrayHelper::getValue($item, 'value'));
                $this->addDescriptionAttribute($attribute);
            }
        }

        return $this->descriptionAttribute;
    }

    public function addDescriptionAttribute(SpuDescriptionAttribute $descriptionAttribute): self
    {
        if (!$this->descriptionAttribute->contains($descriptionAttribute)) {
            $this->descriptionAttribute->add($descriptionAttribute);
            $descriptionAttribute->setSpu($this);
        }

        return $this;
    }

    /**
     * 返回缩略图.
     */
    #[Groups(['restful_read'])]
    public function getMainThumb(): string
    {
        // 有主图我们就用主图
        if ($this->getMainPic()) {
            return $this->getMainPic();
        }

        if (empty($this->getThumbs())) {
            return '';
        }

        return $this->getThumbs()[0]['url'];
    }

    #[Groups(['restful_read'])]
    public function getLimitConfig(): ?array
    {
        if ($this->getLimitRules()->isEmpty()) {
            return null;
        }

        $result = [];
        foreach ($this->getLimitRules() as $limitRule) {
            $result[$limitRule->getType()->value] = $limitRule->getValue();
        }

        return $result;
    }

    /**
     * @return Collection<int, SpuLimitRule>
     */
    public function getLimitRules(): Collection
    {
        return $this->limitRules;
    }

    /**
     * 这里计算所有可能的销售价格
     * TODO 少了时间段的判断.
     */
    public function getSalePrices(): array
    {
        $result = [];
        $cm = Kernel::container()->get(CurrencyManager::class);

        foreach ($this->getSkus() as $sku) {
            foreach ($sku->getSalePrices() as $price) {
                if (!isset($result[$price->getCurrency()])) {
                    $result[$price->getCurrency()] = [
                        'currency' => $price->getCurrency(),
                        'name' => $price->getCurrency(),
                        'minSalePrice' => 0,
                        'maxSalePrice' => 0,
                        'minTaxPrice' => 0,
                        'maxTaxPrice' => 0,
                        'minTaxRate' => 0,
                        'maxTaxRate' => 0,
                    ];

                    if ($cm->getCurrencyByCode($price->getCurrency())) {
                        $result[$price->getCurrency()] = [
                            'currency' => $price->getCurrency(),
                            'name' => $cm->getCurrencyName($price->getCurrency()),
                            'minSalePrice' => 0,
                            'maxSalePrice' => 0,
                            'minTaxPrice' => 0,
                            'maxTaxPrice' => 0,
                            'minTaxRate' => 0,
                            'maxTaxRate' => 0,
                        ];
                    }
                }

                $current = floatval($price->getPrice());
                if (0 === $result[$price->getCurrency()]['minSalePrice'] || $result[$price->getCurrency()]['minSalePrice'] >= $current) {
                    $result[$price->getCurrency()]['minSalePrice'] = $current;
                }
                if ($result[$price->getCurrency()]['maxSalePrice'] <= $current) {
                    $result[$price->getCurrency()]['maxSalePrice'] = $current;
                }

                $current = floatval($price->getTaxRate());
                if (0 === $result[$price->getCurrency()]['minTaxRate'] || $result[$price->getCurrency()]['minTaxRate'] >= $current) {
                    $result[$price->getCurrency()]['minTaxRate'] = $current;
                }
                if ($result[$price->getCurrency()]['maxTaxRate'] <= $current) {
                    $result[$price->getCurrency()]['maxTaxRate'] = $current;
                }

                $current = floatval($price->getTaxPrice());
                if (0 === $result[$price->getCurrency()]['minTaxPrice'] || $result[$price->getCurrency()]['minTaxPrice'] >= $current) {
                    $result[$price->getCurrency()]['minTaxPrice'] = $current;
                }
                if ($result[$price->getCurrency()]['maxTaxPrice'] <= $current) {
                    $result[$price->getCurrency()]['maxTaxPrice'] = $current;
                }
            }
        }

        // print_r($result);exit;
        return array_values($result);
    }

    /**
     * 展示的最终销售价格
     */
    public function getDisplaySalePrice(): string
    {
        $result = [];
        foreach ($this->getSalePrices() as $price) {
            if (0 === $price['minSalePrice']) {
                $money = Kernel::container()->get(CurrencyManager::class)->getPriceNumber($price['maxSalePrice']);
            } else {
                $minSalePrice = Kernel::container()->get(CurrencyManager::class)->getPriceNumber($price['minSalePrice']);
                $maxSalePrice = Kernel::container()->get(CurrencyManager::class)->getPriceNumber($price['maxSalePrice']);
                $money = $price['minSalePrice'] == $price['maxSalePrice']
                    ? $minSalePrice
                    : "{$minSalePrice}~{$maxSalePrice}";
            }

            $result[] = "{$money}{$price['name']}";
        }

        return implode('+', $result);
    }

    /**
     * 展示含税价格
     */
    public function getDisplayTaxPrice(): string
    {
        $result = [];
        foreach ($this->getSalePrices() as $price) {
            if (0 === $price['minTaxPrice']) {
                $money = Kernel::container()->get(CurrencyManager::class)->getPriceNumber($price['maxTaxPrice']);
            } else {
                $minTaxPrice = Kernel::container()->get(CurrencyManager::class)->getPriceNumber($price['minTaxPrice']);
                $maxTaxPrice = Kernel::container()->get(CurrencyManager::class)->getPriceNumber($price['maxTaxPrice']);
                $money = $price['minTaxPrice'] == $price['maxTaxPrice']
                    ? $minTaxPrice
                    : "{$minTaxPrice}~{$maxTaxPrice}";
            }

            $result[] = "{$money}{$price['name']}";
        }

        return implode('+', $result);
    }

    public function retrieveCheckoutArray(): array
    {
        $descriptionAttribute = [];
        foreach ($this->getDescriptionAttribute() as $item) {
            $descriptionAttribute[] = $item->retrieveCheckoutArray();
        }

        return [
            'id' => $this->getId(),
            'supplier' => $this->getSupplier()?->retrievePlainArray(),
            'gtin' => $this->getGtin(),
            'title' => $this->getTitle(),
            'subtitle' => $this->getSubtitle(),
            'type' => $this->getType(),
            'mainPic' => $this->getMainPic(),
            'thumbs' => $this->getThumbs(),
            'descriptionAttribute' => $descriptionAttribute,
            'content' => $this->getContent(),
            'mainThumb' => $this->getMainThumb(),
        ];
    }

    public function getResourceId(): string
    {
        return (string) $this->getId();
    }

    public function getResourceLabel(): string
    {
        return $this->getTitle();
    }
}
