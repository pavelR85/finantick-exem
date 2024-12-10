<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'trade')]
class Trade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'trades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Agent $agent = null;

    #[ORM\Column(type: 'float')]
    private float $tradeSize;

    #[ORM\Column(type: 'integer')]
    private int $lotCount;

    #[ORM\Column(type: 'float')]
    private float $pnl;

    #[ORM\Column(type: 'float')]
    private float $payout;

    #[ORM\Column(type: 'float')]
    private float $usedMargin;

    #[ORM\Column(type: 'float')]
    private float $entryRate;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $closeRate = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateCreated;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateClose = null;

    #[ORM\Column(type: 'string')]
    private string $status;

    #[ORM\Column(type: 'string')]
    private string $position;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $stopLoss = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $takeProfit = null;

    // Add getters and setters

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTradeSize(): ?float
    {
        return $this->tradeSize;
    }

    public function setTradeSize(float $tradeSize): static
    {
        $this->tradeSize = $tradeSize;

        return $this;
    }

    public function getLotCount(): ?int
    {
        return $this->lotCount;
    }

    public function setLotCount(int $lotCount): static
    {
        $this->lotCount = $lotCount;

        return $this;
    }

    public function getPnl(): ?float
    {
        return $this->pnl;
    }

    public function setPnl(float $pnl): static
    {
        $this->pnl = $pnl;

        return $this;
    }

    public function getPayout(): ?float
    {
        return $this->payout;
    }

    public function setPayout(float $payout): static
    {
        $this->payout = $payout;

        return $this;
    }

    public function getUsedMargin(): ?float
    {
        return $this->usedMargin;
    }

    public function setUsedMargin(float $usedMargin): static
    {
        $this->usedMargin = $usedMargin;

        return $this;
    }

    public function getEntryRate(): ?float
    {
        return $this->entryRate;
    }

    public function setEntryRate(float $entryRate): static
    {
        $this->entryRate = $entryRate;

        return $this;
    }

    public function getCloseRate(): ?float
    {
        return $this->closeRate;
    }

    public function setCloseRate(?float $closeRate): static
    {
        $this->closeRate = $closeRate;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateClose(): ?\DateTimeInterface
    {
        return $this->dateClose;
    }

    public function setDateClose(?\DateTimeInterface $dateClose): static
    {
        $this->dateClose = $dateClose;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getStopLoss(): ?float
    {
        return $this->stopLoss;
    }

    public function setStopLoss(?float $stopLoss): static
    {
        $this->stopLoss = $stopLoss;

        return $this;
    }

    public function getTakeProfit(): ?float
    {
        return $this->takeProfit;
    }

    public function setTakeProfit(?float $takeProfit): static
    {
        $this->takeProfit = $takeProfit;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): static
    {
        $this->agent = $agent;

        return $this;
    }
}
