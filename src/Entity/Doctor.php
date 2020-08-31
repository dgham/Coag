<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Hospital;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Expose;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctorRepository")
 */
class Doctor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Expose
     * @Serializer\Groups({"users","admin","doctors"})
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="doctors")
     * @ORM\JoinColumn(nullable=false)
     * @Expose
     * @Serializer\Groups({"users","admin","doctors"})
     */
    private $created_by;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     */
    private $updated_by;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     */
    private $removed_by;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"users","admin","doctors"})
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Hospital")
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\Groups({"users","doctors"})
     * @Expose
     */
    private $hospital;

    /**
     * @ORM\Column(type="boolean")
     */
    private $removed;

    /**
    * @Serializer\Groups({"users","admin","doctors"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $matricule;

    /**
     * @ORM\Column(type="boolean", nullable=false)
    * @Serializer\Groups({"users","admin","doctors"})
     */
    private $affiliate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Speciality")
     * @Expose
     * @Serializer\Groups({"users","admin","doctors"})
     */
    private $speciality;



    public function __construct()
    {
        $this->patients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;

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

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getAffiliate(): ?bool
    {
        return $this->affiliate;
    }

    public function setAffiliate(?bool $affiliate): self
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    public function getSpeciality(): ?Speciality
    {
        return $this->speciality;
    }

    public function setSpeciality(?Speciality $speciality): self
    {
        $this->speciality = $speciality;

        return $this;
    }



 
}
