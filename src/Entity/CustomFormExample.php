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
use F0ska\AutoGridBundle\Attribute\Entity\ActionFormType;
use F0ska\AutoGridBundle\Attribute\Entity\Template;
use F0ska\AutoGridBundle\Attribute\EntityField\Filterable;
use F0ska\AutoGridBundle\Attribute\EntityField\FormOptions;
use F0ska\AutoGridBundle\Attribute\EntityField\FormType;
use F0ska\AutoGridBundle\Attribute\EntityField\ViewTemplate;
use F0ska\AutoGridBundle\Attribute\Permission;
use F0ska\AutoGridBundle\ValueObject\TemplateArea;
use F0ska\AutoGridTestBundle\Form\CustomFormExampleType;
use F0ska\AutoGridTestBundle\Repository\CustomFormExampleRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[ORM\Entity(repositoryClass: CustomFormExampleRepository::class)]
#[ActionFormType(CustomFormExampleType::class)]
#[Template(area: TemplateArea::BEFORE, templatePath: '@F0skaAutoGridTest/customization/grid_before.html.twig')]
#[Template(area: TemplateArea::AFTER, templatePath: '@F0skaAutoGridTest/customization/grid_after.html.twig')]
#[ORM\Index(name: 'custom_form_status_idx', columns: ['status'])]
class CustomFormExample
{
    public const STATUS_NEW      = 'new';
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    #[ViewTemplate('@F0skaAutoGridTest/customization/image.html.twig')]
    #[Permission('grid', allow: false)]
    /**
     * @var string|resource|null $file
     */
    private $file = null;

    #[ORM\Column(length: 64)]
    private ?string $title = null;

    #[ORM\Column(length: 32)]
    #[FormType(ChoiceType::class)]
    #[FormOptions([
        'choices' => [
            'New'      => self::STATUS_NEW,
            'Pending'  => self::STATUS_PENDING,
            'Approved' => self::STATUS_APPROVED,
            'Rejected' => self::STATUS_REJECTED,
        ],
    ])]
    #[Filterable] // Smart Filter Fallback should pick up ChoiceType and options
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Permission('grid', allow: false)]
    private ?string $note = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?string
    {
        if (null === $this->file) {
            return null;
        }
        return base64_encode(fread($this->file, $this->getFileSize()));
    }

    public function getFileSize(): int
    {
        if (null === $this->file) {
            return 0;
        }
        return fstat($this->file)['size'];
    }

    public function setFile(?string $file): static
    {
        $this->file = $file;

        return $this;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }
}
