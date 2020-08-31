<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
/**
 * @ORM\Entity(repositoryClass="App\Repository\FoodsRepository")
 */
class Foods
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"users","admin","doctors","patients"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Serializer\Groups({"users","admin","doctors","patients"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"users","admin","doctors","patients"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Serializer\Groups({"users","admin","doctors","patients"})
     */
    private $quantity;

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
     * @ORM\Column(type="boolean")
     */
    private $removed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): self
    {
        $this->quantity = $quantity;

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
