<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    const GROUP_POST = "post_group";

    const GROUP_SEND = "send";

    const STATUS_NEW = 0;

    const STATUS_WAITING = 1;

    const STATUS_PROCESSED = 2;

    const STATUS_ERROR = 3;

    const STATUS_COMPLETED = 4;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({Post::GROUP_POST, Post::GROUP_SEND})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice({"GET", "POST", "PUT", "DELETE"})
     * @Groups(Post::GROUP_POST)
     */
    private $method;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(Post::GROUP_POST)
     * @Assert\Url
     */
    private $url;

    /**
     * @ORM\Column(type="datetime")
     * @Groups(Post::GROUP_POST)
     * @Assert\DateTime
     */
    private $timeExecute;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(Post::GROUP_POST)
     * @Assert\NotBlank
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Expose(if="true")
     * @Groups(Post::GROUP_POST)
     */
    private $user;

    /**
     * @ORM\Column(type="smallint")
     * @Groups(Post::GROUP_POST)
     */
    private $status;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $requestDuration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTimeExecute(): ?\DateTimeInterface
    {
        return $this->timeExecute;
    }

    public function setTimeExecute(\DateTimeInterface $timeExecute): self
    {
        $this->timeExecute = $timeExecute;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRequestDuration(): ?float
    {
        return $this->requestDuration;
    }

    public function setRequestDuration(?float $requestDuration): self
    {
        $this->requestDuration = $requestDuration;

        return $this;
    }
}
