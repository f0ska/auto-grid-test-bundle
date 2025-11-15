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

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use F0ska\AutoGridTestBundle\Config\ExampleEnum;
use F0ska\AutoGridTestBundle\Repository\OtherTypesExampleRepository;

#[ORM\Entity(repositoryClass: OtherTypesExampleRepository::class)]
class OtherTypesExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: ExampleEnum::class)]
    private ?ExampleEnum $enumType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $decimalType = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $guidType = null;

    #[ORM\Column(type: 'date_point')]
    private ?DateTimeInterface $datePointType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnumType(): ?ExampleEnum
    {
        return $this->enumType;
    }

    public function setEnumType(ExampleEnum $enumType): static
    {
        $this->enumType = $enumType;

        return $this;
    }

    public function getDecimalType(): ?string
    {
        return $this->decimalType;
    }

    public function setDecimalType(string $decimalType): static
    {
        $this->decimalType = $decimalType;

        return $this;
    }

    public function getGuidType(): ?string
    {
        return $this->guidType;
    }

    public function setGuidType(string $guidType): static
    {
        $this->guidType = $guidType;

        return $this;
    }

    public function getDatePointType(): ?DateTimeInterface
    {
        return $this->datePointType;
    }

    public function setDatePointType(DateTimeInterface $datePointType): static
    {
        $this->datePointType = $datePointType;

        return $this;
    }
}
