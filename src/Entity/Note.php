<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoteRepository")
 */
class Note
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"doctors"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Groups({"doctors"})
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"doctors"})
     */
    private $created_by;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     */
    private $upadated_by;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     */
    private $removed_by;

    /**
     * @ORM\Column(type="datetime")
    * @Serializer\Groups({"doctors"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $upadted_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $removed_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $remove;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"doctors"})
     */
    private $patient_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

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

    public function getUpadatedBy(): ?user
    {
        return $this->upadated_by;
    }

    public function setUpadatedBy(?user $upadated_by): self
    {
        $this->upadated_by = $upadated_by;

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

    public function getUpadtedAt(): ?\DateTimeInterface
    {
        return $this->upadted_at;
    }

    public function setUpadtedAt(?\DateTimeInterface $upadted_at): self
    {
        $this->upadted_at = $upadted_at;

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

    public function getRemove(): ?bool
    {
        return $this->remove;
    }

    public function setRemove(bool $remove): self
    {
        $this->remove = $remove;

        return $this;
    }

    public function getPatientId(): ?user
    {
        return $this->patient_id;
    }

    public function setPatientId(?user $patient_id): self
    {
        $this->patient_id = $patient_id;

        return $this;
    }
}
