<?php

namespace App\Entity;

use App\Repository\CampaignRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
#[ORM\Entity(repositoryClass: CampaignRepository::class)]
#[Vich\Uploadable]
class Campaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $stardDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'campaigns')]
    private ?Client $client = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $baseArt = null;

    #[Vich\UploadableField(mapping: 'baseArtImage',fileNameProperty: 'baseArt')]
    private ?File $baseArtFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $imgUpdatedAt = null;


    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'campaign')]
    private Collection $posts;

    #[ORM\Column(nullable: true)]
    private ?int $banner_height = null;

    #[ORM\Column(nullable: true)]
    private ?int $banner_width = null;

    /**
     * @var Collection<int, CampaignStructure>
     */
    #[ORM\OneToMany(targetEntity: CampaignStructure::class, mappedBy: 'campaign')]
    private Collection $campaignStructures;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $postingTime = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->campaignStructures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStardDate(): ?\DateTimeInterface
    {
        return $this->stardDate;
    }

    public function setStardDate(?\DateTimeInterface $stardDate): static
    {
        $this->stardDate = $stardDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getBaseArt(): ?string
    {
        return $this->baseArt;
    }

    public function setBaseArt(?string $baseArt): static
    {
        $this->baseArt = $baseArt;

        return $this;
    }


    public function getBaseArtFile(): ?File
    {
        return $this->baseArtFile;
    }

    public function setBaseArtFile(?File $baseArtFile = null): void
    {
        $this->baseArtFile = $baseArtFile;
        if (null !== $baseArtFile) {
            $this->imgUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getImgUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->imgUpdatedAt;
    }

    public function setImgUpdatedAt(?\DateTimeImmutable $imgUpdatedAt): void
    {
        $this->imgUpdatedAt = $imgUpdatedAt;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setCampaign($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCampaign() === $this) {
                $post->setCampaign(null);
            }
        }

        return $this;
    }


    public function getBannerHeight(): ?int
    {
        return $this->banner_height;
    }

    public function setBannerHeight(?int $banner_height): static
    {
        $this->banner_height = $banner_height;

        return $this;
    }

    public function getBannerWidth(): ?int
    {
        return $this->banner_width;
    }

    public function setBannerWidth(?int $banner_width): static
    {
        $this->banner_width = $banner_width;

        return $this;
    }

    /**
     * @return Collection<int, CampaignStructure>
     */
    public function getCampaignStructures(): Collection
    {
        return $this->campaignStructures;
    }

    public function addCampaignStructure(CampaignStructure $campaignStructure): static
    {
        if (!$this->campaignStructures->contains($campaignStructure)) {
            $this->campaignStructures->add($campaignStructure);
            $campaignStructure->setCampaign($this);
        }

        return $this;
    }

    public function removeCampaignStructure(CampaignStructure $campaignStructure): static
    {
        if ($this->campaignStructures->removeElement($campaignStructure)) {
            // set the owning side to null (unless already changed)
            if ($campaignStructure->getCampaign() === $this) {
                $campaignStructure->setCampaign(null);
            }
        }

        return $this;
    }

    public function getPostingTime(): ?\DateTimeInterface
    {
        return $this->postingTime;
    }

    public function setPostingTime(?\DateTimeInterface $postingTime): static
    {
        $this->postingTime = $postingTime;

        return $this;
    }
}
