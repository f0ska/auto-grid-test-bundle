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
use F0ska\AutoGridBundle\Attribute\Permission\Forbid;
use F0ska\AutoGridTestBundle\Repository\BlogArticleExampleRepository;

#[ORM\Entity(repositoryClass: BlogArticleExampleRepository::class)]
#[ORM\Index(name: 'blog_article_title_idx', columns: ['title'])]
#[ORM\Index(name: 'blog_article_published_idx', columns: ['published'])]
#[HasLifecycleCallbacks]
class BlogArticleExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?bool $published = null;

    #[ORM\Column]
    #[Forbid('create')]
    #[Forbid('edit')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Forbid('create')]
    #[Forbid('edit')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?BlogUserExample $author = null;

    /**
     * @var Collection<int, BlogArticleTagExample>
     */
    #[ORM\ManyToMany(targetEntity: BlogArticleTagExample::class)]
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
}
