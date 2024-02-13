<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AuthUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthUserRepository::class)]
#[ApiResource]
class AuthUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $authname = null;

    #[ORM\Column(length: 2500)]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthname(): ?string
    {
        return $this->authname;
    }

    public function setAuthname(string $authname): static
    {
        $this->authname = $authname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
