<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DiagnosticRepository")
 */
class Diagnostic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()s
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"users","doctors"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Groups({"users","doctors"})
     */
    private $value;

    /**
     * @ORM\Column(type="string", length=100)
     * @Serializer\Groups({"users","doctors"})
     */
    private $indication;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="diagnostics")
     * @ORM\JoinColumn(nullable=false)
     *  @Serializer\Groups({"doctors"})
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
     * @Serializer\Groups({"users","doctors"})
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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $device_date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $details;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getIndication(): ?string
    {
        return $this->indication;
    }

    public function setIndication(string $indication): self
    {
        $this->indication = $indication;

        return $this;
    }

    public function getCreatedBy(): ?user
    {
        return $this->created_by;
    }

    public function setCreatedBy(?user $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getUpdatedBy(): ?user
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?user $updated_by): self
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getRemovedBy(): ?user
    {
        return $this->removed_by;
    }

    public function setRemovedBy(?user $removed_by): self
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

    public function getDeviceDate(): ?string
    {
        return $this->device_date;
    }

    public function setDeviceDate(?string $device_date): self
    {
        $this->device_date = $device_date;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }
}
