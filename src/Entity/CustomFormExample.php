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
use F0ska\AutoGridBundle\Attribute\Entity\FormType;
use F0ska\AutoGridBundle\Attribute\Entity\ViewTemplate;
use F0ska\AutoGridBundle\Attribute\Permission\Forbid;
use F0ska\AutoGridTestBundle\Form\CustomFormExampleType;
use F0ska\AutoGridTestBundle\Repository\CustomFormExampleRepository;

#[ORM\Entity(repositoryClass: CustomFormExampleRepository::class)]
#[FormType(CustomFormExampleType::class)]
class CustomFormExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    #[ViewTemplate('@F0skaAutoGridTest/customization/image.html.twig')]
    #[Forbid('grid')]
    /**
     * @var string|resource|null $file
     */
    private $file = null;

    #[ORM\Column(length: 64)]
    private ?string $title = null;

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
}
