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
use F0ska\AutoGridBundle\Attribute\EntityField\AssociatedField;
use F0ska\AutoGridBundle\Attribute\EntityField\Filterable;
use F0ska\AutoGridBundle\Attribute\EntityField\Label;
use F0ska\AutoGridBundle\Attribute\EntityField\Position;
use F0ska\AutoGridBundle\Attribute\EntityField\Sortable;
use F0ska\AutoGridBundle\Attribute\EntityField\VirtualColumn;
use F0ska\AutoGridBundle\Attribute\Permission;
use F0ska\AutoGridBundle\Condition\AssociationCondition; // Changed from InCondition
use F0ska\AutoGridTestBundle\Repository\BlogArticleExampleRepository;

#[ORM\Entity(repositoryClass: BlogArticleExampleRepository::class)]
#[HasLifecycleCallbacks]
class BlogArticleExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Position(-100)]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 64)]
    #[Filterable]
    #[Sortable]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Filterable]
    #[Permission('grid', allow: false)] // Hide long text from grid, but keep in view/edit
    private ?string $content = null;

    #[ORM\Column]
    #[Filterable]
    #[Sortable]
    private ?bool $published = null;

    #[ORM\Column]
    #[Permission('create', allow: false)]
    #[Permission('edit', allow: false)]
    #[Sortable(direction: 'desc')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Permission('create', allow: false)]
    #[Permission('edit', allow: false)]
    #[Permission('grid', allow: false)] // Hide from grid
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[AssociatedField(name: 'username', label: 'Author', position: 10)]
    #[AssociatedField(name: 'email', label: 'Author Email', position: 11)]
    #[AssociatedField(name: 'articlesCount', label: 'Author Posts', position: 12)]
    #[Permission(allow: false)] // Hide the author object itself
    private ?BlogUserExample $author = null;

    /**
     * @var Collection<int, BlogArticleTagExample>
     */
    #[ORM\ManyToMany(targetEntity: BlogArticleTagExample::class)]
    #[Label("Tags")]
    #[Filterable(condition: AssociationCondition::class, formOptions: ['multiple' => true])] // Changed condition
    private Collection $tags;

    #[VirtualColumn(dql: "SELECT COUNT(c.id) FROM F0ska\AutoGridTestBundle\Entity\BlogArticleCommentExample c WHERE c.article = {this}")]
    #[Label("Comments")]
    #[Sortable]
    private ?int $commentsCount = null;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
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

    public function getAuthor(): ?BlogUserExample
    {
        return $this->author;
    }

    public function setAuthor(?BlogUserExample $author): static
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

    public function getCommentsCount(): ?int
    {
        return $this->commentsCount;
    }

    public function setCommentsCount(?int $commentsCount): self
    {
        $this->commentsCount = $commentsCount;

        return $this;
    }
}
