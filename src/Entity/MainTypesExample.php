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
use F0ska\AutoGridTestBundle\Repository\MainTypesExampleRepository;

#[ORM\Entity(repositoryClass: MainTypesExampleRepository::class)]
class MainTypesExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $stringType = null;

    #[ORM\Column(type: Types::ASCII_STRING)]
    private $asciiStringType = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $textType = null;

    #[ORM\Column]
    private ?bool $booleanType = null;

    #[ORM\Column]
    private ?int $integerType = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $smallintType = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $bigintType = null;

    #[ORM\Column]
    private ?float $floatType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStringType(): ?string
    {
        return $this->stringType;
    }

    public function setStringType(string $stringType): static
    {
        $this->stringType = $stringType;

        return $this;
    }

    public function getAsciiStringType()
    {
        return $this->asciiStringType;
    }

    public function setAsciiStringType($asciiStringType): static
    {
        $this->asciiStringType = $asciiStringType;

        return $this;
    }

    public function getTextType(): ?string
    {
        return $this->textType;
    }

    public function setTextType(string $textType): static
    {
        $this->textType = $textType;

        return $this;
    }

    public function isBooleanType(): ?bool
    {
        return $this->booleanType;
    }

    public function setBooleanType(bool $booleanType): static
    {
        $this->booleanType = $booleanType;

        return $this;
    }

    public function getIntegerType(): ?int
    {
        return $this->integerType;
    }

    public function setIntegerType(int $integerType): static
    {
        $this->integerType = $integerType;

        return $this;
    }

    public function getSmallintType(): ?int
    {
        return $this->smallintType;
    }

    public function setSmallintType(int $smallintType): static
    {
        $this->smallintType = $smallintType;

        return $this;
    }

    public function getBigintType(): ?string
    {
        return $this->bigintType;
    }

    public function setBigintType(string $bigintType): static
    {
        $this->bigintType = $bigintType;

        return $this;
    }

    public function getFloatType(): ?float
    {
        return $this->floatType;
    }

    public function setFloatType(float $floatType): static
    {
        $this->floatType = $floatType;

        return $this;
    }
}
