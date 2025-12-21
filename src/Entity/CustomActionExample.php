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
use F0ska\AutoGridBundle\Attribute\Entity\MassAction;
use F0ska\AutoGridBundle\Attribute\Entity\RouteCreate;
use F0ska\AutoGridBundle\Attribute\Entity\RouteDelete;
use F0ska\AutoGridBundle\Attribute\Entity\RouteEdit;
use F0ska\AutoGridBundle\Attribute\Entity\RouteView;
use F0ska\AutoGridBundle\Attribute\EntityField\CanFilter;
use F0ska\AutoGridBundle\Attribute\EntityField\CanSort;
use F0ska\AutoGridBundle\Attribute\EntityField\RangeFilter;
use F0ska\AutoGridTestBundle\Repository\CustomActionExampleRepository;

#[ORM\Entity(repositoryClass: CustomActionExampleRepository::class)]
#[RouteCreate]
#[RouteEdit]
#[RouteView]
#[RouteDelete]
#[MassAction('Custom Mass Action')]
#[MassAction('Another Custom Mass Action')]
#[MassAction(name: 'Custom Action with Custom Redirect', code: 'custom_action_redirect')]
class CustomActionExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[RangeFilter(true)]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 32)]
    #[CanSort(true)]
    #[CanFilter((true))]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[CanSort(true)]
    #[CanFilter((true))]
    private ?string $description = null;

    #[ORM\Column]
    #[CanSort(true)]
    #[CanFilter((true))]
    private ?bool $enabled = null;

    #[ORM\Column]
    #[CanSort(true)]
    #[CanFilter((true))]
    #[RangeFilter(true)]
    private ?\DateTimeImmutable $publishAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPublishAt(): ?\DateTimeImmutable
    {
        return $this->publishAt;
    }

    public function setPublishAt(\DateTimeImmutable $publishAt): static
    {
        $this->publishAt = $publishAt;

        return $this;
    }
}
