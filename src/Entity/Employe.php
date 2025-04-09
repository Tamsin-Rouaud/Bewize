<?php

namespace App\Entity;

use App\Enum\EmployeStatus;
use App\Repository\EmployeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
class Employe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(min: 3)]
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Assert\Length(min: 3)]
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
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
}
