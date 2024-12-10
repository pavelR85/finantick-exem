<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'asset')]
class Asset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 8)]
    private float $bid;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 8)]
    private string $ask;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $dateUpdate;

    #[ORM\Column(type: 'integer')]
    private int $lotSize = 10;

    #[ORM\Column(type: 'string')]
    private string $assetName;

    // Add getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBid(): ?float
    {
        return $this->bid;
    }

    public function setBid(float $bid): static
    {
        $this->bid = $bid;

        return $this;
    }

    public function getAsk(): ?float
    {
        return $this->ask;
    }

    public function setAsk(float $ask): static
    {
        $this->ask = $ask;

        return $this;
    }

    public function getLotSize(): ?int
    {
        return $this->lotSize;
    }

    public function setLotSize(int $lotSize): static
    {
        $this->lotSize = $lotSize;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(\DateTimeInterface $dateUpdate): static
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    public function getAssetName(): ?string
    {
        return $this->assetName;
    }

    public function setAssetName(string $assetName): static
    {
        $this->assetName = $assetName;

        return $this;
    }
}
