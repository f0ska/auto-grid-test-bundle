<?php
/*
 * This file is part of the F0ska/AutoGridTest package.
 *
 * (c) Victor Shvets
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace F0ska\AutoGridTestBundle\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use F0ska\AutoGridBundle\Attribute\Entity\AdvancedFilter;
use F0ska\AutoGridBundle\Attribute\Entity\Fieldset;
use F0ska\AutoGridBundle\Attribute\EntityField\AddToFieldset;
use F0ska\AutoGridBundle\Attribute\EntityField\CanFilter;
use F0ska\AutoGridBundle\Attribute\EntityField\CanSort;
use F0ska\AutoGridBundle\Attribute\EntityField\FieldTemplate;
use F0ska\AutoGridBundle\Attribute\EntityField\GridTruncate;
use F0ska\AutoGridBundle\Attribute\EntityField\Position;
use F0ska\AutoGridBundle\Attribute\Permission\Allow;
use F0ska\AutoGridBundle\Attribute\Permission\AllowAll;
use F0ska\AutoGridBundle\Attribute\Permission\DisallowActionsByDefault;
use F0ska\AutoGridBundle\Attribute\Permission\DisallowFieldsByDefault;
use F0ska\AutoGridBundle\Attribute\Permission\Forbid;
use F0ska\AutoGridTestBundle\Repository\AdvancedArticleExampleRepository;

#[ORM\Entity(repositoryClass: AdvancedArticleExampleRepository::class)]
#[ORM\Index(name: 'adv_article_title_idx', columns: ['title'])]
#[ORM\Index(name: 'adv_article_published_idx', columns: ['published'])]
#[HasLifecycleCallbacks]
#[DisallowActionsByDefault]
#[DisallowFieldsByDefault]
#[Allow('grid')]
#[Allow('view')]
#[Allow('advanced_filter')]
#[AdvancedFilter(true)]
#[Fieldset(name: 'Some')]
#[Fieldset(name: 'Things', fields: ['createdAt', 'updatedAt'])]
#[Fieldset(name: 'Here', class: 'col-12', fields: ['content'])]
class AdvancedArticleExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 80)]
    #[CanFilter(true)]
    #[CanSort(true)]
    #[AllowAll]
    #[AddToFieldset('Some')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[AllowAll]
    #[GridTruncate(100)]
    private ?string $content = null;

    #[ORM\Column]
    #[CanFilter(true)]
    #[AllowAll]
    #[Forbid('grid')]
    #[AddToFieldset('Some')]
    private ?bool $published = null;

    #[ORM\Column]
    #[Allow('view')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Allow('view')]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Allow('view')]
    #[AddToFieldset('Some')]
    #[Position(-1)]
    #[FieldTemplate('@F0skaAutoGridTest/customization/profile_link.html.twig')]
    private ?AdvancedUserExample $author = null;

    /**
     * @var Collection<int, BlogArticleTagExample>
     */
    #[ORM\ManyToMany(targetEntity: BlogArticleTagExample::class)]
    #[Allow('view')]
    #[AddToFieldset('Things')]
    #[FieldTemplate('@F0skaAutoGridTest/customization/tag_filter_link.html.twig')]
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
