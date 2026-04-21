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

use Doctrine\ORM\Mapping as ORM;
use F0ska\AutoGridBundle\Attribute\EntityField\Filterable;
use F0ska\AutoGridBundle\Attribute\EntityField\Label;
use F0ska\AutoGridBundle\Attribute\EntityField\Sortable;
use F0ska\AutoGridBundle\Attribute\EntityField\VirtualColumn;
use F0ska\AutoGridBundle\Attribute\Permission;
use F0ska\AutoGridBundle\Condition\RangeCondition;
use F0ska\AutoGridTestBundle\Repository\BlogUserExampleRepository;

#[ORM\Entity(repositoryClass: BlogUserExampleRepository::class)]
class BlogUserExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 255)]
    #[Permission(action: 'grid', allow: false, gridId: 'articles')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 15)]
    private ?string $lastIp = null;

    #[ORM\Column]
    private ?bool $banned = false;

    #[VirtualColumn(dql: "SELECT COUNT(a.id) FROM F0ska\AutoGridTestBundle\Entity\BlogArticleExample a WHERE a.author = {this}")]
    #[Label("Articles Count")]
    #[Sortable]
    #[Filterable(condition: RangeCondition::class)]
    private ?int $articlesCount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getLastIp(): ?string
    {
        return $this->lastIp;
    }

    public function setLastIp(string $lastIp): static
    {
        $this->lastIp = $lastIp;

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->banned;
    }

    public function setBanned(?bool $banned): static
    {
        $this->banned = $banned;

        return $this;
    }

    public function getArticlesCount(): ?int
    {
        return $this->articlesCount;
    }

    public function setArticlesCount(?int $articlesCount): self
    {
        $this->articlesCount = $articlesCount;

        return $this;
    }
}
