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
use F0ska\AutoGridTestBundle\Repository\BlogUserExampleRepository;

#[ORM\Entity(repositoryClass: BlogUserExampleRepository::class)]
class BlogUserExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $userName = null;

    #[ORM\Column(length: 15)]
    private ?string $lastIp = null;

    #[ORM\Column]
    private ?bool $banned = false;

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

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

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
}
