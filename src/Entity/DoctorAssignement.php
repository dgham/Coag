<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctorAssignementRepository")
 */
class DoctorAssignement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $id_doctor;

    /**
     * @ORM\Column(type="datetime")
     */
    private $request_date;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean")
     */
    private $removed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPatient(): ?User
    {
        return $this->id_patient;
    }

    public function setIdPatient(?User $id_patient): self
    {
        $this->id_patient = $id_patient;

        return $this;
    }

    public function getIdDoctor(): ?User
    {
        return $this->id_doctor;
    }

    public function setIdDoctor(?User $id_doctor): self
    {
        $this->id_doctor = $id_doctor;

        return $this;
    }

    public function getRequestDate(): ?\DateTimeInterface
    {
        return $this->request_date;
    }

    public function setRequestDate(\DateTimeInterface $request_date): self
    {
        $this->request_date = $request_date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $disabled;

        return $this;
    }

    public function getRemoved(): ?bool
    {
        return $this->removed;
    }

    public function setRemoved(bool $removed): self
    {
        $this->removed = $removed;

        return $this;
    }
}
