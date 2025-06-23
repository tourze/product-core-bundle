<?php

namespace ProductCoreBundle\Entity;

use AntdCpBundle\Builder\Field\BraftEditor;
use AntdCpBundle\Builder\Field\DynamicFieldSet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ProductCoreBundle\Repository\CategoryRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnumExtra\Itemable;
use Tourze\TrainCourseBundle\Trait\SortableTrait;

#[ORM\Table(name: 'product_category', options: ['comment' => '产品分类表'])]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category implements \Stringable, Itemable, AdminArrayInterface
{
    use BlameableAware;
    use TimestampableAware;

    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;


    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;
    #[TrackColumn]
    #[Groups(['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;
    #[Groups(['restful_read', 'api_tree'])]
    #[ORM\Column(type: Types::STRING, length: 60, options: ['comment' => '分类名'])]
    private ?string $title = null;
    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Category $parent = null;
    use SortableTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'LOGO地址'])]
    private ?string $logoUrl = null;
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '简介'])]
    private ?string $description = null;
    /**
     * 下级分类列表.
     *
     * @var Collection<Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'parent')]
    private Collection $children;
    /**
     * SPU归属关系.
     *
     * @var Collection<Spu>
     */
    #[ORM\ManyToMany(targetEntity: Spu::class, inversedBy: 'categories', fetch: 'EXTRA_LAZY')]
    private Collection $spus;
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;
    /**
     * @DynamicFieldSet()
     *
     * @var Collection<CategoryLimitRule>
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: CategoryLimitRule::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $limitRules;
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '显示标签'])]
    private ?array $showTags = [];

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->spus = new ArrayCollection();
        $this->limitRules = new ArrayCollection();
    }

    /**
     * @deprecated 兼容旧的写法，直接在这里返回所有数据
     */
    /**
     * @deprecated This method should be moved to a service
     */
    public static function genOptions(): array
    {
        // This method should be refactored to use dependency injection
        // instead of static container access
        return [];
    }

    /**
     * @deprecated 兼容旧的写法，直接在这里返回所有数据
     */
    public static function genTreeData(): array
    {
        // This method should be refactored to use dependency injection
        // instead of static container access
        return [];
    }

    public function getCategoryChildren(bool $is_only_enabled = false): array
    {
        $result = [
            'title' => $this->getTitle(),
            'value' => $this->getId(),
            'key' => $this->getId(),

            'id' => $this->getId(),
            'name' => $this->getTitle(),
        ];

        // 查下一层
        $categories = $this->getChildren();
        if ($categories->count() > 0) {
            $result['children'] = [];
            foreach ($categories as $category) {
                // 如果只查询启用的分类，当前这个分类没有启用的话它的子目录也就不需要再查了
                if ($is_only_enabled && !$category->isValid()) {
                    continue;
                }

                $result['children'][] = $category->getCategoryChildren();
            }
        }

        return $result;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function __toString(): string
    {
        if ($this->getId() === null || $this->getId() === 0) {
            return '';
        }

        return "#{$this->getId()} {$this->getTitle()}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
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

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

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

    public function renderSpuCount(): int
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

    /**
     * @return Collection<int, CategoryLimitRule>
     */
    public function getLimitRules(): Collection
    {
        return $this->limitRules;
    }

    public function addLimitRule(CategoryLimitRule $limitRule): self
    {
        if (!$this->limitRules->contains($limitRule)) {
            $this->limitRules[] = $limitRule;
            $limitRule->setCategory($this);
        }

        return $this;
    }

    public function removeLimitRule(CategoryLimitRule $limitRule): self
    {
        if ($this->limitRules->removeElement($limitRule)) {
            // set the owning side to null (unless already changed)
            if ($limitRule->getCategory() === $this) {
                $limitRule->setCategory(null);
            }
        }

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

    /**
     * 获取可以用来搜索的分类ID
     */
    public function getSearchableId(): array
    {
        $result = [$this->getId()];
        foreach ($this->getChildren() as $child) {
            $result = array_merge($result, $child->getSearchableId());
        }

        return array_values(array_unique($result));
    }

    /**
     * @return Collection<Category>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getNestTitle(): string
    {
        if ($this->getParent() !== null) {
            return "{$this->getParent()->getTitle()}/{$this->getTitle()}";
        }

        return "{$this->getTitle()}";
    }

    public function getSimpleArray(): array
    {
        $result = [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'logoUrl' => $this->getLogoUrl(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'children' => [],
        ];

        // Get children ordered by sortNumber
        $children = $this->getChildren()->toArray();
        usort($children, function ($a, $b) {
            return $b->getSortNumber() <=> $a->getSortNumber();
        });

        foreach ($children as $child) {
            /* @var static $child */
            if ($child !== null) {
                $result['children'][] = $child->getSimpleArray();
            }
        }

        return $result;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'logoUrl' => $this->getLogoUrl(),
            'valid' => $this->isValid(),
            ...$this->retrieveSortableArray(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
