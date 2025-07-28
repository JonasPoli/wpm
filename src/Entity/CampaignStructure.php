<?php

namespace App\Entity;

use App\Repository\CampaignStructureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampaignStructureRepository::class)]
class CampaignStructure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'campaignStructures')]
    private ?Campaign $campaign = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $font = null;

    #[ORM\Column(nullable: true)]
    private ?int $colorR = null;

    #[ORM\Column]
    private ?int $colorG = null;

    #[ORM\Column]
    private ?int $colorB = null;

    #[ORM\Column]
    private ?int $shadowXShift = null;

    #[ORM\Column]
    private ?int $shadowYShif = null;

    #[ORM\Column]
    private ?int $fontSize = null;

    #[ORM\Column]
    private ?float $lineHeight = null;

    #[ORM\Column]
    private ?int $boxX = null;

    #[ORM\Column]
    private ?int $boxY = null;

    #[ORM\Column]
    private ?int $boxWidth = null;

    #[ORM\Column]
    private ?int $boxHeight = null;

    #[ORM\Column(length: 255)]
    private ?string $alignX = null;

    #[ORM\Column(length: 255)]
    private ?string $alignY = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var Collection<int, PostText>
     */
    #[ORM\OneToMany(targetEntity: PostText::class, mappedBy: 'CampaingStructure')]
    private Collection $postTexts;

    public function __construct()
    {
        $this->postTexts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFont(): ?string
    {
        return $this->font;
    }

    public function setFont(?string $font): static
    {
        $this->font = $font;

        return $this;
    }

    public function getColorR(): ?int
    {
        return $this->colorR;
    }

    public function setColorR(?int $colorR): static
    {
        $this->colorR = $colorR;

        return $this;
    }

    public function getColorG(): ?int
    {
        return $this->colorG;
    }

    public function setColorG(int $colorG): static
    {
        $this->colorG = $colorG;

        return $this;
    }

    public function getColorB(): ?int
    {
        return $this->colorB;
    }

    public function setColorB(int $colorB): static
    {
        $this->colorB = $colorB;

        return $this;
    }

    public function getShadowXShift(): ?int
    {
        return $this->shadowXShift;
    }

    public function setShadowXShift(int $shadowXShift): static
    {
        $this->shadowXShift = $shadowXShift;

        return $this;
    }

    public function getShadowYShif(): ?int
    {
        return $this->shadowYShif;
    }

    public function setShadowYShif(int $shadowYShif): static
    {
        $this->shadowYShif = $shadowYShif;

        return $this;
    }

    public function getFontSize(): ?int
    {
        return $this->fontSize;
    }

    public function setFontSize(int $fontSize): static
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    public function getLineHeight(): ?float
    {
        return $this->lineHeight;
    }

    public function setLineHeight(float $lineHeight): static
    {
        $this->lineHeight = $lineHeight;

        return $this;
    }

    public function getBoxX(): ?int
    {
        return $this->boxX;
    }

    public function setBoxX(int $boxX): static
    {
        $this->boxX = $boxX;

        return $this;
    }

    public function getBoxY(): ?int
    {
        return $this->boxY;
    }

    public function setBoxY(int $boxY): static
    {
        $this->boxY = $boxY;

        return $this;
    }

    public function getBoxWidth(): ?int
    {
        return $this->boxWidth;
    }

    public function setBoxWidth(int $boxWidth): static
    {
        $this->boxWidth = $boxWidth;

        return $this;
    }

    public function getBoxHeight(): ?int
    {
        return $this->boxHeight;
    }

    public function setBoxHeight(int $boxHeight): static
    {
        $this->boxHeight = $boxHeight;

        return $this;
    }

    public function getAlignX(): ?string
    {
        return $this->alignX;
    }

    public function setAlignX(string $alignX): static
    {
        $this->alignX = $alignX;

        return $this;
    }

    public function getAlignY(): ?string
    {
        return $this->alignY;
    }

    public function setAlignY(string $alignY): static
    {
        $this->alignY = $alignY;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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
            $postText->setCampaingStructure($this);
        }

        return $this;
    }

    public function removePostText(PostText $postText): static
    {
        if ($this->postTexts->removeElement($postText)) {
            // set the owning side to null (unless already changed)
            if ($postText->getCampaingStructure() === $this) {
                $postText->setCampaingStructure(null);
            }
        }

        return $this;
    }
}
