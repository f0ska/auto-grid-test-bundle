<?php
/*
 * This file is part of the F0ska/AutoGridTest package.
 *
 * (c) Victor Shvets
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace F0ska\AutoGridTestBundle\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use F0ska\AutoGridBundle\Attribute\Entity\ActionButtonDisplay;
use F0ska\AutoGridBundle\Attribute\Entity\AdvancedFilter;
use F0ska\AutoGridBundle\Attribute\Entity\Fieldset;
use F0ska\AutoGridBundle\Attribute\Entity\PageLimits;
use F0ska\AutoGridBundle\Attribute\Entity\RedirectOnSubmit;
use F0ska\AutoGridBundle\Attribute\EntityField\AddToFieldset;
use F0ska\AutoGridBundle\Attribute\EntityField\AssociatedField;
use F0ska\AutoGridBundle\Attribute\EntityField\ColumnHtmlClass;
use F0ska\AutoGridBundle\Attribute\EntityField\Filterable;
use F0ska\AutoGridBundle\Attribute\EntityField\GridTruncate;
use F0ska\AutoGridBundle\Attribute\EntityField\Position;
use F0ska\AutoGridBundle\Attribute\EntityField\Sortable;
use F0ska\AutoGridBundle\Attribute\EntityField\ValuePrefix;
use F0ska\AutoGridBundle\Attribute\EntityField\ViewTemplate;
use F0ska\AutoGridBundle\Attribute\Permission;
use F0ska\AutoGridBundle\Attribute\Permission\DisallowActionsByDefault;
use F0ska\AutoGridBundle\Attribute\Permission\DisallowFieldsByDefault;
use F0ska\AutoGridTestBundle\Repository\AdvancedArticleExampleRepository;

#[ORM\Entity(repositoryClass: AdvancedArticleExampleRepository::class)]
#[ORM\Index(name: 'adv_article_title_idx', columns: ['title'])]
#[ORM\Index(name: 'adv_article_published_idx', columns: ['published'])]
#[HasLifecycleCallbacks]
#[DisallowActionsByDefault]
#[DisallowFieldsByDefault]
#[Permission('grid')]
#[Permission('view')]
#[Permission('advanced_filter')]
#[Permission('edit')]
#[ActionButtonDisplay('edit', displayOnGrid: false)]
#[AdvancedFilter(true)]
#[Fieldset(name: 'Content Info')]
#[Fieldset(name: 'Metatags')]
#[Fieldset(name: 'Full Content')]
#[PageLimits([5])]
#[RedirectOnSubmit('grid')]
class AdvancedArticleExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ValuePrefix("#")]
    #[Permission]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 80)]
    #[Filterable]
    #[Sortable]
    #[Permission]
    #[AddToFieldset('Content Info')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Permission]
    #[GridTruncate(50)]
    #[AddToFieldset('Full Content')]
    private ?string $content = null;

    #[ORM\Column]
    #[Filterable]
    #[Permission]
    #[Permission('grid', allow: false)]
    #[Permission('edit', gridId: 'specific_grid_id')]
    #[AddToFieldset('Content Info')]
    private ?bool $published = null;

    #[ORM\Column]
    #[Permission('grid')]
    #[Permission('view')]
    #[AddToFieldset('Metatags')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Permission('view')]
    #[Permission('grid', allow: false)]
    #[AddToFieldset('Metatags')]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Permission('grid')]
    #[Permission('view')]
    #[AddToFieldset('Content Info')]
    #[Position(-1)]
    #[ViewTemplate('@F0skaAutoGridTest/customization/profile_link.html.twig')]
    #[AssociatedField(name: 'email', label: 'Author contact', position: 5)]
    private ?AdvancedUserExample $author = null;

    /**
     * @var Collection<int, BlogArticleTagExample>
     */
    #[ORM\ManyToMany(targetEntity: BlogArticleTagExample::class)]
    #[Permission('grid')]
    #[Permission('view')]
    #[AddToFieldset('Metatags')]
    #[ViewTemplate('@F0skaAutoGridTest/customization/tag_filter_link.html.twig')]
    #[ColumnHtmlClass('col-1')]
    private Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[PrePersist]
    #[PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function getAuthor(): ?AdvancedUserExample
    {
        return $this->author;
    }

    public function setAuthor(?AdvancedUserExample $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, BlogArticleTagExample>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(BlogArticleTagExample $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(BlogArticleTagExample $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
