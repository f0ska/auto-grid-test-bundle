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
use F0ska\AutoGridBundle\Attribute\Entity\ActionRoute;
use F0ska\AutoGridBundle\Attribute\Entity\ExportAction;
use F0ska\AutoGridBundle\Attribute\Entity\HtmlClass;
use F0ska\AutoGridBundle\Attribute\Entity\MassAction;
use F0ska\AutoGridBundle\Attribute\Entity\Template;
use F0ska\AutoGridBundle\Attribute\EntityField\Filterable;
use F0ska\AutoGridBundle\Attribute\EntityField\Sortable;
use F0ska\AutoGridBundle\Attribute\EntityField\ColumnHtmlClass;
use F0ska\AutoGridBundle\Condition\RangeCondition;
use F0ska\AutoGridBundle\ValueObject\TemplateArea;
use F0ska\AutoGridTestBundle\Repository\CustomActionExampleRepository;

#[ORM\Entity(repositoryClass: CustomActionExampleRepository::class)]
#[ActionRoute('create')]
#[ActionRoute('edit')]
#[ActionRoute('view')]
#[ActionRoute('delete')]
#[MassAction('Custom Mass Action')]
#[MassAction('Another Custom Mass Action')]
#[MassAction(name: 'Custom Action with Custom Redirect', code: 'custom_action_redirect')]
#[ExportAction(name: 'Export Action Example', code: 'export_example')]
#[Template(area: TemplateArea::GRID_COLUMN_VALUE_ACTION, templatePath: '@F0skaAutoGridTest/customization/grid_action.html.twig')]
#[HtmlClass(table: 'table-striped')]
class CustomActionExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Filterable(condition: RangeCondition::class)]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 32)]
    #[Sortable]
    #[Filterable]
    #[ColumnHtmlClass(headerClass: 'col-2')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Sortable]
    #[Filterable]
    #[ColumnHtmlClass(headerClass: 'col-4')]
    private ?string $description = null;

    #[ORM\Column]
    #[Sortable]
    #[Filterable]
    private ?bool $enabled = null;

    #[ORM\Column]
    #[Sortable]
    #[Filterable]
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
