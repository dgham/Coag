<?php

namespace App\Entity;

use App\Entity\Gender;
use App\Entity\UserType;
use App\Entity\GenderType;
use Webmozart\Assert\Assert;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Expose;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Image;


/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     * @Serializer\Groups({"users","admin","doctors"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=85, nullable=true)
     * @Expose
     * @Serializer\Groups({"users","doctors","admin","hospitals"})
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Expose
     * @Serializer\Groups({"users","doctors","admin","hospitals"})
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Expose
     * @Serializer\Groups({"users"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=55)
     * @Expose
     * @Serializer\Groups({"users"})
     */
    private $userType;

    /**
     * @Expose
     * @ORM\Column(type="date", nullable=true)
     * @Serializer\Groups({"users","doctors","admin","hospitals"})
     */
    private $birth_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $session_timeout;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Expose
     * @Serializer\Groups({"users"})
     */
    private $zip_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Expose
     * @Serializer\Groups({"users","doctors"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Expose
     * @Serializer\Groups({"users","doctors","hospitals"})
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $multi_session;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $language;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Expose
     * @Serializer\Groups({"users","doctors","admin","hospitals"})
     */
    private $picture;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     * @ORM\JoinColumn(nullable=true)
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     */
    private $removed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $remove;

    public function __construct()
    {
        parent::__construct();
       
    }
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->passwordord,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized,['allowed_classes' => false]);
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender($gender = null): self
    {
        if (!GenderType::isValidValue($gender)) {
            throw new \InvalidArgumentException('Invalid gender Type (Accepted Values: ' . GenderType::valuesString() .')');
        }
        $this->gender = $gender;
        return $this;
      
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType($userType=null)
    {
        if (!UserType::isValidValue($userType)) {

            throw new \InvalidArgumentException('Invalid User Type (Accepted Values: ' . UserType::valuesString() .')');
        }
        
        $this->userType = $userType;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(?\DateTimeInterface $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getSessionTimeout(): ?\DateTimeInterface
    {
        return $this->session_timeout;
    }

    public function setSessionTimeout(?\DateTimeInterface $session_timeout): self
    {
        $this->session_timeout = $session_timeout;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(?string $zip_code): self
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMultiSession(): ?bool
    {
        return $this->multi_session;
    }

    public function setMultiSession(?bool $multi_session): self
    {
        $this->multi_session = $multi_session;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

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

  

    public function getCreated(): ?user
    {
        return $this->created;
    }

    public function setCreated(?user $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?user
    {
        return $this->updated;
    }

    public function setUpdated(?user $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getRemoved(): ?user
    {
        return $this->removed;
    }

    public function setRemoved(?user $removed): self
    {
        $this->removed = $removed;

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
    
}