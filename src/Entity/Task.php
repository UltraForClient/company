<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="street", type="string", length=255)
     */
    private $street;


    /**
     * @ORM\Column(name="house_number", type="string", length=32)
     */
    private $houseNumber;


    /**
     * @ORM\Column(name="post_code", type="string", length=64)
     */
    private $postCode;

    /**
     * @ORM\Column(name="place", type="string", length=64)
     */
    private $place;

    /**
     * @ORM\Column(name="task", type="string", length=64)
     */
    private $task;

    /**
     * @ORM\Column(name="comments", type="text")
     */
    private $comments;

    /**
     * @ORM\Column(name="valuation", type="string", length=64, nullable=true)
     */
    private $valuation;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="task", fileNameProperty="image")
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(name="published_at", type="datetime")
     */
    private $publishedAt;

    /**
     * @ORM\Column(name="updated_at",type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }


    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(string $postCode): void
    {
        $this->postCode = $postCode;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): void
    {
        $this->place = $place;
    }

    public function getTask(): ?string
    {
        return $this->task;
    }

    public function setTask(string $task): void
    {
        $this->task = $task;
    }

    public function setComments(string $comments): void
    {
        $this->comments = $comments;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function getValuation()
    {
        return $this->valuation;
    }

    public function setValuation($valuation): void
    {
        $this->valuation = $valuation;
    }

    public function setImage(?string $image = null): void
    {
        $this->image = $image;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImageFile(File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
