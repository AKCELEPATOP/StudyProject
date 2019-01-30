<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 29.01.2019
 * Time: 10:38
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity(repositoryClass="App\Repository\TaskRepository")*/
class Task
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tasks")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $method;

    /**
     * @ORM\Column(type="string")
     */
    protected $url;

    /**
     * @ORM\Column(type="integer")
     */
    protected $speed;

    /**
     * @ORM\Column(type="datеtime")
     */
    protected $timeExecute;

    /**
     * @ORM\Column(type="string")
     */
    protected $body;

    /**
     * Task constructor.
     * @param $id
     * @param $user
     * @param $method
     * @param $url
     * @param $speed
     * @param $timeExecute
     * @param $body
     */
    public function __construct($id, $user, $method, $url, $speed, $timeExecute, $body)
    {
        $this->id = $id;
        $this->user = $user;
        $this->method = $method;
        $this->url = $url;
        $this->speed = $speed;
        $this->timeExecute = $timeExecute;
        $this->body = $body;
    }


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

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getTimeExecute()
    {
        return $this->timeExecute;
    }

    public function setTimeExecute($timeExecute): self
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
}