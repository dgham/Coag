<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctorAssignementRepository")
 */
class DoctorAssignement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"doctors"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"doctors"})
     */
    private $id_patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     *  @Serializer\Groups({"doctors"})
     */
    private $id_doctor;

    /**
     * @ORM\Column(type="datetime")
     *  @Serializer\Groups({"doctors"})
     */
    private $request_date;

    /**
     * @ORM\Column(type="string", length=100)
     *  @Serializer\Groups({"doctors"})
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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $created_by;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $updated_by;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $removed_by;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $removed_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $invitation_token;

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
        $this->enabled = $enabled;

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

    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?User $updated_by): self
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getRemovedBy(): ?User
    {
        return $this->removed_by;
    }

    public function setRemovedBy(?User $removed_by): self
    {
        $this->removed_by = $removed_by;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getRemovedAt(): ?\DateTimeInterface
    {
        return $this->removed_at;
    }

    public function setRemovedAt(?\DateTimeInterface $removed_at): self
    {
        $this->removed_at = $removed_at;

        return $this;
    }

    public function getInvitationToken(): ?string
    {
        return $this->invitation_token;
    }

    public function setInvitationToken(string $invitation_token): self
    {
        $this->invitation_token = $invitation_token;

        return $this;
    }
}
