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
use F0ska\AutoGridBundle\Attribute\EntityField\GridTruncate;
use F0ska\AutoGridTestBundle\Repository\ArrayObjectTypesExampleRepository;

#[ORM\Entity(repositoryClass: ArrayObjectTypesExampleRepository::class)]
class ArrayObjectTypesExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY)]
    #[GridTruncate(20)]
    private array $arrayType = [];

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    #[GridTruncate(20)]
    private array $simpleArray = [];

    #[ORM\Column]
    #[GridTruncate(20)]
    private array $jsonType = [];

    #[ORM\Column(type: Types::OBJECT)]
    #[GridTruncate(20)]
    private ?object $objectType = null;

    #[ORM\Column(type: Types::BINARY)]
    private $binaryType = null;

    #[ORM\Column(type: Types::BLOB)]
    private $blobType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArrayType(): array
    {
        return $this->arrayType;
    }

    public function setArrayType(array $arrayType): static
    {
        $this->arrayType = $arrayType;

        return $this;
    }

    public function getSimpleArray(): array
    {
        return $this->simpleArray;
    }

    public function setSimpleArray(array $simpleArray): static
    {
        $this->simpleArray = $simpleArray;

        return $this;
    }

    public function getJsonType(): array
    {
        return $this->jsonType;
    }

    public function setJsonType(array $jsonType): static
    {
        $this->jsonType = $jsonType;

        return $this;
    }

    public function getObjectType(): ?object
    {
        return $this->objectType;
    }

    public function setObjectType(object $objectType): static
    {
        $this->objectType = $objectType;

        return $this;
    }

    public function getBinaryType()
    {
        return $this->binaryType;
    }

    public function setBinaryType($binaryType): static
    {
        $this->binaryType = $binaryType;

        return $this;
    }

    public function getBlobType()
    {
        return $this->blobType;
    }

    public function setBlobType($blobType): static
    {
        $this->blobType = $blobType;

        return $this;
    }
}
