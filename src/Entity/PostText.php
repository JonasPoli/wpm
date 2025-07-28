<?php

namespace App\Entity;

use App\Repository\PostTextRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostTextRepository::class)]
class PostText
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'postTexts')]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'postTexts')]
    private ?CampaignStructure $CampaingStructure = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCampaingStructure(): ?CampaignStructure
    {
        return $this->CampaingStructure;
    }

    public function setCampaingStructure(?CampaignStructure $CampaingStructure): static
    {
        $this->CampaingStructure = $CampaingStructure;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
