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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use F0ska\AutoGridBundle\Attribute\EntityField\AssociatedField;
use F0ska\AutoGridBundle\Attribute\EntityField\GridTruncate;
use F0ska\AutoGridBundle\Attribute\Permission;
use F0ska\AutoGridTestBundle\Repository\BlogArticleCommentExampleRepository;

#[ORM\Entity(repositoryClass: BlogArticleCommentExampleRepository::class)]
class BlogArticleCommentExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[AssociatedField(name: 'username', label: 'Author')]
    #[Permission(allow: false)]
    private ?BlogUserExample $author = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[GridTruncate(22)]
    private ?BlogArticleExample $article = null;

    #[ORM\Column(type: Types::TEXT)]
    #[GridTruncate(24)]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getArticle(): ?BlogArticleExample
    {
        return $this->article;
    }

    public function setArticle(?BlogArticleExample $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
