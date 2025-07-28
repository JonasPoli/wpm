<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $scheduleDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'imageImage',fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $imgUpdatedAt = null;


    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?Campaign $campaign = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?User $approvedBy = null;

    #[ORM\Column(nullable: true)]
    private ?int $midia = null;

    /**
     * @var Collection<int, PostText>
     */
    #[ORM\OneToMany(targetEntity: PostText::class, mappedBy: 'post')]
    private Collection $postTexts;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textToPublish = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebookId = null;

    /**
     * @var Collection<int, PostHistory>
     */
    #[ORM\OneToMany(targetEntity: PostHistory::class, mappedBy: 'post')]
    private Collection $postHistories;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dinamicImage = null;

    #[ORM\Column(nullable: true)]
    private ?int $backgroundImageMarginY = null;

    #[ORM\Column(nullable: true)]
    private ?int $backgroundImageMarginX = null;

    public function __construct()
    {
        $this->postTexts = new ArrayCollection();
        $this->postHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getScheduleDate(): ?\DateTimeInterface
    {
        return $this->scheduleDate;
    }

    public function setScheduleDate(?\DateTimeInterface $scheduleDate): static
    {
        $this->scheduleDate = $scheduleDate;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }


    public function getimageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setimageFile(?File $imageFile = null):void
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile){
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

    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(?Campaign $campaign): static
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getApprovedBy(): ?User
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?User $approvedBy): static
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    public function getMidia(): ?int
    {
        return $this->midia;
    }

    public function setMidia(?int $midia): static
    {
        $this->midia = $midia;

        return $this;
    }

    /**
     * @return Collection<int, PostText>
     */
    public function getPostTexts(): Collection
    {
        return $this->postTexts;
    }

    public function addPostText(PostText $postText): static
    {
        if (!$this->postTexts->contains($postText)) {
            $this->postTexts->add($postText);
            $postText->setPost($this);
        }

        return $this;
    }

    public function removePostText(PostText $postText): static
    {
        if ($this->postTexts->removeElement($postText)) {
            // set the owning side to null (unless already changed)
            if ($postText->getPost() === $this) {
                $postText->setPost(null);
            }
        }

        return $this;
    }

    public function getTextToPublish(): ?string
    {
        return $this->textToPublish;
    }

    public function setTextToPublish(?string $textToPublish): static
    {
        $this->textToPublish = $textToPublish;

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): static
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * @return Collection<int, PostHistory>
     */
    public function getPostHistories(): Collection
    {
        return $this->postHistories;
    }

    public function addPostHistory(PostHistory $postHistory): static
    {
        if (!$this->postHistories->contains($postHistory)) {
            $this->postHistories->add($postHistory);
            $postHistory->setPost($this);
        }

        return $this;
    }

    public function removePostHistory(PostHistory $postHistory): static
    {
        if ($this->postHistories->removeElement($postHistory)) {
            // set the owning side to null (unless already changed)
            if ($postHistory->getPost() === $this) {
                $postHistory->setPost(null);
            }
        }

        return $this;
    }

    public function getDinamicImage(): ?string
    {
        return $this->dinamicImage;
    }

    public function setDinamicImage(?string $dinamicImage): static
    {
        $this->dinamicImage = $dinamicImage;

        return $this;
    }

    public function getArrayTexts(): array
    {
        $texts = [];
        foreach ($this->getPostTexts() as $postText) {
            $texts[] = [
                'content' => $postText->getContent(),
                'font' => $postText->getCampaingStructure()->getFont(),
                'color' => [$postText->getCampaingStructure()->getColorR(), $postText->getCampaingStructure()->getColorG(), $postText->getCampaingStructure()->getColorB()], // RGB color
                'shadow' => [$postText->getCampaingStructure()->getShadowXShift(), $postText->getCampaingStructure()->getShadowYShif()], // Shadow X and Y offset
                'fontSize' => $postText->getCampaingStructure()->getFontSize(),
                'lineHeight' => $postText->getCampaingStructure()->getLineHeight(),
                'box' => [$postText->getCampaingStructure()->getBoxX(), $postText->getCampaingStructure()->getBoxY(), $postText->getCampaingStructure()->getBoxWidth(), $postText->getCampaingStructure()->getBoxHeight()], // X, Y, Width, Height
                'align' => [$postText->getCampaingStructure()->getAlignX(), $postText->getCampaingStructure()->getAlignY()] // Horizontal and vertical alignment
            ];
        }
        return $texts;
    }

    public function getBackgroundImageMarginY(): ?int
    {
        return $this->backgroundImageMarginY;
    }

    public function setBackgroundImageMarginY(?int $backgroundImageMarginY): static
    {
        $this->backgroundImageMarginY = $backgroundImageMarginY;

        return $this;
    }

    public function getBackgroundImageMarginX(): ?int
    {
        return $this->backgroundImageMarginX;
    }

    public function setBackgroundImageMarginX(?int $backgroundImageMarginX): static
    {
        $this->backgroundImageMarginX = $backgroundImageMarginX;

        return $this;
    }
}
