<?php

namespace App\Entity;

use App\Entity\Food;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EatingHabitRepository")
 */
class EatingHabit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"users","doctors","patients"})
     */
    private $id;
        
    /**
     * @ORM\Column(type="string", length=1024)
     * @Serializer\Groups({"users","doctors","patients"})
     */
    private $food;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Groups({"users","doctors","patients"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Groups({"users","doctors","patients"})
     */
    private $unit;
 

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="eatinghabit")
     * @ORM\JoinColumn(nullable=false)
      * @Serializer\Groups({"users","doctors","patients"})
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
     * @Serializer\Groups({"users","doctors","patients"})
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
     * @ORM\Column(type="boolean")
     */
    private $remove;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRemove(): ?bool
    {
        return $this->remove;
    }

    public function setRemove(bool $remove): self
    {
        $this->remove = $remove;

        return $this;
    }

    public function getFoodDescription(): ?Food
    {
        return $this->food_description;
    }

    public function setFoodDescription(?Food $food_description): self
    {
        $this->food_description = $food_description;

        return $this;
    }

    public function getBreakfastFood(): ?string
    {
        return $this->breakfastFood;
    }

    public function setBreakfastFood(?string $breakfastFood): self
    {
        $this->breakfastFood = $breakfastFood;

        return $this;
    }

    public function getLunchFood(): ?string
    {
        return $this->lunchFood;
    }

    public function setLunchFood(?string $lunchFood): self
    {
        $this->lunchFood = $lunchFood;

        return $this;
    }

    public function getDinnerFood(): ?string
    {
        return $this->dinnerFood;
    }

    public function setDinnerFood(string $dinnerFood): self
    {
        $this->dinnerFood = $dinnerFood;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getFood(): ?string
    {
        return $this->food;
    }

    public function setFood(string $food): self
    {
        $this->food = $food;

        return $this;
    }

   
}
