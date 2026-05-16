<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entity für die Employee = Darstellung einer Datenbanktabelle als PHP-Klasse
// Jede Eigenschaft der Klasse entspricht einer Spalte in der Datenbank.
#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 20)]
    private ?string $zip_code = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $type_of_employment = null;

    #[ORM\Column(length: 255)]
    private ?string $department = null;

    #[ORM\Column(length: 50)]
    private ?string $email_address = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $date_of_entry = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $date_of_leaving = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(string $zip_code): static
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getTypeOfEmployment(): ?string
    {
        return $this->type_of_employment;
    }

    public function setTypeOfEmployment(string $type_of_employment): static
    {
        $this->type_of_employment = $type_of_employment;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->email_address;
    }

    public function setEmailAddress(string $email_address): static
    {
        $this->email_address = $email_address;

        return $this;
    }

    public function getDateOfEntry(): ?DateTimeInterface
    {
        return $this->date_of_entry;
    }

    public function setDateOfEntry(DateTimeInterface $d): static
    {
        $this->date_of_entry = $d;
        return $this;
    }

    public function getDateOfLeaving(): ?DateTimeInterface
    {
        return $this->date_of_leaving;
    }

    public function setDateOfLeaving(?DateTimeInterface $d): static
    {
        $this->date_of_leaving = $d;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }
}
