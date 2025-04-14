<?php

namespace App\Entity;

use App\Enum\EmployeStatus;
use App\Repository\EmployeRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Employe implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 180, nullable:false)]
    private ?string $nom = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 180, nullable:false)]
    private ?string $prenom = null;

    #[Assert\Choice(callback: [EmployeStatus::class, 'cases'], message: 'Veuillez choisir un statut valide.')]
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?EmployeStatus $status = null;

    #[Assert\Email(
        message: 'Le mail saisi : {{ value }} n\'est pas valide.',
    )]
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $email = null;
    
    
    #[Assert\NotBlank()]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_entree = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json', nullable:true)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_STRONG)]
    #[Assert\NotCompromisedPassword]
    #[ORM\Column]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getDateEntree(): ?\DateTimeImmutable
    {
        return $this->date_entree;
    }

    public function setDateEntree(\DateTimeImmutable $date_entree): static
    {
        $this->date_entree = $date_entree;

        return $this;
    }

    public function getStatus(): ?EmployeStatus
    {
        return $this->status;
    }

    public function setStatus(EmployeStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getInitiales(): string
    {

        $initialePrenom = $this->prenom ? strtoupper($this->prenom[0]) : '';
        $initialeNom = $this->nom ? strtoupper($this->nom[0]) : '';

        return $initialePrenom . $initialeNom;
    }

    public function __toString(): string
    {
        return $this->prenom . ' ' . $this->nom;
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
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

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

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

}
