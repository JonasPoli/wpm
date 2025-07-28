<?php

namespace App\Entity;

use App\Repository\PostHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostHistoryRepository::class)]
class PostHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $eventDescription = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $occurredIn = null;

    #[ORM\ManyToOne(inversedBy: 'postHistories')]
    private ?Post $post = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventDescription(): ?string
    {
        return $this->eventDescription;
    }

    public function setEventDescription(?string $eventDescription): static
    {
        $this->eventDescription = $eventDescription;

        return $this;
    }

    public function getOccurredIn(): ?\DateTimeInterface
    {
        return $this->occurredIn;
    }

    public function setOccurredIn(?\DateTimeInterface $occurredIn): static
    {
        $this->occurredIn = $occurredIn;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }
}
