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
use F0ska\AutoGridBundle\Attribute\Entity\FormType;
use F0ska\AutoGridBundle\Attribute\EntityField\FormOptions;
use F0ska\AutoGridBundle\Attribute\EntityField\Label;
use F0ska\AutoGridBundle\Attribute\Permission\Allow;
use F0ska\AutoGridBundle\Attribute\Permission\ForbidAll;
use F0ska\AutoGridTestBundle\Repository\AdvancedUserExampleRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: AdvancedUserExampleRepository::class)]
class AdvancedUserExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 255)]
    #[Email]
    #[FormType(EmailType::class)]
    #[FormOptions(['required' => true, 'help' => 'Enter your real email please!'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Length(min: 3)]
    #[Label('Nickname')]
    private ?string $userName = null;

    #[ORM\Column(length: 15)]
    #[ForbidAll]
    #[Allow('view')]
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
