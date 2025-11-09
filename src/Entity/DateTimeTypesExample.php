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
use F0ska\AutoGridTestBundle\Repository\DateTimeTypesExampleRepository;

#[ORM\Entity(repositoryClass: DateTimeTypesExampleRepository::class)]
class DateTimeTypesExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $datetimeType = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datetimeImmutableType = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $datetimetzType = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private ?\DateTimeImmutable $datetimetzImmutableType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateType = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateImmutableType = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $timeType = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $timeImmutableType = null;

    #[ORM\Column]
    private ?\DateInterval $dateintervalType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetimeType(): ?\DateTime
    {
        return $this->datetimeType;
    }

    public function setDatetimeType(\DateTime $datetimeType): static
    {
        $this->datetimeType = $datetimeType;

        return $this;
    }

    public function getDatetimeImmutableType(): ?\DateTimeImmutable
    {
        return $this->datetimeImmutableType;
    }

    public function setDatetimeImmutableType(\DateTimeImmutable $datetimeImmutableType): static
    {
        $this->datetimeImmutableType = $datetimeImmutableType;

        return $this;
    }

    public function getDatetimetzType(): ?\DateTime
    {
        return $this->datetimetzType;
    }

    public function setDatetimetzType(\DateTime $datetimetzType): static
    {
        $this->datetimetzType = $datetimetzType;

        return $this;
    }

    public function getDatetimetzImmutableType(): ?\DateTimeImmutable
    {
        return $this->datetimetzImmutableType;
    }

    public function setDatetimetzImmutableType(\DateTimeImmutable $datetimetzImmutableType): static
    {
        $this->datetimetzImmutableType = $datetimetzImmutableType;

        return $this;
    }

    public function getDateType(): ?\DateTime
    {
        return $this->dateType;
    }

    public function setDateType(\DateTime $dateType): static
    {
        $this->dateType = $dateType;

        return $this;
    }

    public function getDateImmutableType(): ?\DateTimeImmutable
    {
        return $this->dateImmutableType;
    }

    public function setDateImmutableType(\DateTimeImmutable $dateImmutableType): static
    {
        $this->dateImmutableType = $dateImmutableType;

        return $this;
    }

    public function getTimeType(): ?\DateTime
    {
        return $this->timeType;
    }

    public function setTimeType(\DateTime $timeType): static
    {
        $this->timeType = $timeType;

        return $this;
    }

    public function getTimeImmutableType(): ?\DateTimeImmutable
    {
        return $this->timeImmutableType;
    }

    public function setTimeImmutableType(\DateTimeImmutable $timeImmutableType): static
    {
        $this->timeImmutableType = $timeImmutableType;

        return $this;
    }

    public function getDateintervalType(): ?\DateInterval
    {
        return $this->dateintervalType;
    }

    public function setDateintervalType(\DateInterval $dateintervalType): static
    {
        $this->dateintervalType = $dateintervalType;

        return $this;
    }
}
