<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    /**
     * @OA\Property(type="integer",
     *     description="The unique identifier of the user.")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @OA\Property(
     *     type="string",
     *     description="The first name of the user.",
     *     example="John")
     */
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    /**
     * @OA\Property(
     *     type="string",
     *     description="The last name of the user.",
     *     example="Doe")
     */
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    /**
     * @OA\Property(
     *     type="string",
     *     description="The email address of the user.",
     *     example="jdoe@example.com")
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @OA\Property(
     *     type="integer",
     *     description="The print quota of the user.",
     *     example=100)
     */
    #[ORM\Column]
    private ?int $printQuota = null;

    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="string"),
     *     description="The roles of the user.",
     *     example={"ROLE_USER"})
     */
    #[ORM\Column]
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPrintQuota(): ?int
    {
        return $this->printQuota;
    }

    public function setPrintQuota(int $printQuota): self
    {
        $this->printQuota = $printQuota;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
