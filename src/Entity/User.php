<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\InheritanceType('JOINED')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: "string", length: 8)]
    private ?string $numTel = null;

    #[ORM\Column(type: "text")]
    private ?string $address = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isVerified = false;
    
    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }
    
    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;
    
        return $this;
    }
    

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $user_agent = null;

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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Garantir que chaque utilisateur a ce rôle
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
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

    public function getNumTel(): ?string 
    { 
        return $this->numTel; 
    }

    public function setNumTel(string $numTel): self 
    { 
        $this->numTel = $numTel; 
        return $this; 
    }

    public function getAddress(): ?string 
    { 
        return $this->address; 
    }

    public function setAddress(string $address): self 
    { 
        $this->address = $address; 
        return $this; 
    }

    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?string $verificationCode): self
    {
        $this->verificationCode = $verificationCode;
        return $this;
    }
 

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Nettoyage des données sensibles (si nécessaire)
    }

    public function getSalt(): ?string
    {
        return null; // Pas besoin de salage supplémentaire
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(?string $user_agent): static
    {
        $this->user_agent = $user_agent;
        return $this;
    }
}
