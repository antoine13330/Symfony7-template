<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\AuthUserRepository;
use App\Controller\RegisterController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AuthUserRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            name: 'register',
            uriTemplate: '/register',
            controller: RegisterController::class,
            openapiContext: [
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'json',
                                'properties' => [
                                    'username' => [
                                        'type' => 'string',
                                        'example' => 'username@gmail.com',
                                    ],
                                    'password' => [
                                        'type' => 'string',
                                        'example' => 'password',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'responseBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'json',
                                'properties' => [
                                    'user' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => [
                                                'type' => 'integer',
                                                'example' => 1,
                                            ],
                                            'username' => [
                                                'type' => 'string',
                                                'example' => 'username@gmail.com',
                                            ],
                                            'roles' => [
                                                'type' => 'array',
                                                'example' => ['ROLE_USER'],
                                            ],
                                        ],
                                    ],
                                    'token' => [
                                        'type' => 'string',
                                        'example' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjYwNzIwNzYsImV4cCI6MTYyNjA3NTY3Niwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidXNlcm5hbWVAZ21haWwuY29tIn0.0'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        )
    ],
)]
class AuthUser implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', unique: true, nullable: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['user:deep'])]
    private ?string $username = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['user:deep'])]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: 'Your password must be at least {{ limit }} characters long',
        maxMessage: 'Your password cannot be longer than {{ limit }} characters',
    )]
    private ?string $password = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

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
    public function eraseCredentials(): void
    {
        return;
    }
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }
}
