<?php

namespace App\Entity;

use App\Repository\InvatedUsersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvatedUsersRepository::class)
 */
class InvatedUsers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $inviter;

    /**
     * @ORM\Column(type="integer")
     */
    private $invitation;
    

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $errorTxt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $errorCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fromGroup;

    public function __construct() {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInviter(): ?int
    {
        return $this->inviter;
    }

    public function setInviter(int $inviter): self
    {
        $this->inviter = $inviter;

        return $this;
    }

    public function getInvitation(): ?int
    {
        return $this->invitation;
    }

    public function setInvitation(int $invitation): self
    {
        $this->invitation = $invitation;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getErrorTxt(): ?string
    {
        return $this->errorTxt;
    }

    public function setErrorTxt(string $errorTxt): self
    {
        $this->errorTxt = $errorTxt;

        return $this;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function setErrorCode(string $errorCode): self
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFromGroup(): ?string
    {
        return $this->fromGroup;
    }

    public function setFromGroup(?string $fromGroup): self
    {
        $this->fromGroup = $fromGroup;

        return $this;
    }
}
