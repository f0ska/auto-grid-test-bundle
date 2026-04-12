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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use F0ska\AutoGridBundle\Attribute\Entity\HtmlClass;
use F0ska\AutoGridBundle\Attribute\Entity\Title;
use F0ska\AutoGridBundle\Attribute\EntityField\Filterable;
use F0ska\AutoGridBundle\Attribute\EntityField\FormOptions;
use F0ska\AutoGridBundle\Attribute\EntityField\FormType;
use F0ska\AutoGridBundle\Attribute\EntityField\Label;
use F0ska\AutoGridBundle\Attribute\EntityField\Position;
use F0ska\AutoGridBundle\Attribute\EntityField\Sortable;
use F0ska\AutoGridBundle\Attribute\EntityField\ValuePrefix;
use F0ska\AutoGridTestBundle\Repository\CorporateClientExampleRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[ORM\Entity(repositoryClass: CorporateClientExampleRepository::class)]
#[Title("Corporate Clients Dashboard")]
#[HtmlClass('table-sm is-narrow')]
class CorporateClientExample
{
    private const STATUSES = [
        'Active' => 'active',
        'Pending' => 'pending',
        'Inactive' => 'inactive',
        'Archived' => 'archived',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Position(1)]
    #[Label("ID")]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 255)]
    #[Position(2)]
    #[Label("Company Name")]
    #[Sortable]
    #[Filterable]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Position(3)]
    #[Label("Primary Contact")]
    #[Filterable]
    private ?string $contactEmail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Position(4)]
    #[Label("Annual Revenue")]
    #[ValuePrefix("$ ")]
    #[Sortable]
    private ?string $revenue = null;

    #[ORM\Column(length: 20)]
    #[Position(5)]
    #[Label("Account Status")]
    #[FormType(ChoiceType::class)]
    #[FormOptions(['choices' => self::STATUSES])]
    #[Filterable] // Automatically inherits ChoiceType and options from the form attributes
    private string $status = "active";

    #[ORM\Column]
    #[Position(6)]
    #[Label("Last Audit")]
    #[Sortable]
    private ?\DateTimeImmutable $lastAuditAt = null;

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

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getRevenue(): ?string
    {
        return $this->revenue;
    }

    public function setRevenue(string $revenue): static
    {
        $this->revenue = $revenue;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLastAuditAt(): ?\DateTimeImmutable
    {
        return $this->lastAuditAt;
    }

    public function setLastAuditAt(\DateTimeImmutable $lastAuditAt): static
    {
        $this->lastAuditAt = $lastAuditAt;

        return $this;
    }
}
