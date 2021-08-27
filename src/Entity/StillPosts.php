<?php

namespace App\Entity;

use App\Repository\StillPostsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StillPostsRepository::class)
 */
class StillPosts
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
    private $botId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $groupId;

    /**
     * @ORM\Column(type="integer")
     */
    private $postId;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $userId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $error;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $errorTxt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBotId(): ?int
    {
        return $this->botId;
    }

    public function setBotId(int $botId): self
    {
        $this->botId = $botId;

        return $this;
    }

    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    public function setGroupId(string $groupId): self
    {
        $this->groupId = $groupId;

        return $this;
    }

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): self
    {
        $this->postId = $postId;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(string $error): self
    {
        $this->error = $error;

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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }
}
