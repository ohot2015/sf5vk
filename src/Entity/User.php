<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $longSm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $diameter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fromHeigh;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toHeight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $FromWeight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toWeight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fromAge;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toAge;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fromLongSm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toLongSm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fromDiameter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toDiameter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $searchGender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $orientation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $searchOrientation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getLongSm(): ?int
    {
        return $this->longSm;
    }

    public function setLongSm(int $longSm): self
    {
        $this->longSm = $longSm;

        return $this;
    }

    public function getDiameter(): ?string
    {
        return $this->diameter;
    }

    public function setDiameter(string $diameter): self
    {
        $this->diameter = $diameter;

        return $this;
    }

    public function getFromHeigh(): ?string
    {
        return $this->fromHeigh;
    }

    public function setFromHeigh(string $fromHeigh): self
    {
        $this->fromHeigh = $fromHeigh;

        return $this;
    }

    public function getToHeight(): ?string
    {
        return $this->toHeight;
    }

    public function setToHeight(?string $toHeight): self
    {
        $this->toHeight = $toHeight;

        return $this;
    }

    public function getFromWeight(): ?string
    {
        return $this->FromWeight;
    }

    public function setFromWeight(string $FromWeight): self
    {
        $this->FromWeight = $FromWeight;

        return $this;
    }

    public function getToWeight(): ?string
    {
        return $this->toWeight;
    }

    public function setToWeight(string $toWeight): self
    {
        $this->toWeight = $toWeight;

        return $this;
    }

    public function getFromAge(): ?string
    {
        return $this->fromAge;
    }

    public function setFromAge(string $fromAge): self
    {
        $this->fromAge = $fromAge;

        return $this;
    }

    public function getToAge(): ?string
    {
        return $this->toAge;
    }

    public function setToAge(string $toAge): self
    {
        $this->toAge = $toAge;

        return $this;
    }

    public function getFromLongSm(): ?string
    {
        return $this->fromLongSm;
    }

    public function setFromLongSm(string $fromLongSm): self
    {
        $this->fromLongSm = $fromLongSm;

        return $this;
    }

    public function getToLongSm(): ?string
    {
        return $this->toLongSm;
    }

    public function setToLongSm(string $toLongSm): self
    {
        $this->toLongSm = $toLongSm;

        return $this;
    }

    public function getFromDiameter(): ?string
    {
        return $this->fromDiameter;
    }

    public function setFromDiameter(string $fromDiameter): self
    {
        $this->fromDiameter = $fromDiameter;

        return $this;
    }

    public function getToDiameter(): ?string
    {
        return $this->toDiameter;
    }

    public function setToDiameter(string $toDiameter): self
    {
        $this->toDiameter = $toDiameter;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getSearchGender(): ?string
    {
        return $this->searchGender;
    }

    public function setSearchGender(string $searchGender): self
    {
        $this->searchGender = $searchGender;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getSearchOrientation(): ?string
    {
        return $this->searchOrientation;
    }

    public function setSearchOrientation(string $searchOrientation): self
    {
        $this->searchOrientation = $searchOrientation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
