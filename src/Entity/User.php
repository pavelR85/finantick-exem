<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[Assert\Unique(message: 'This username is already taken.')]
    #[ORM\Column(type: 'string', unique: true)]
    private string $username;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $loginTime = null;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $totalPnl = 0;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $equity = 0;

    #[ORM\ManyToOne(targetEntity: Agent::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Agent $agent = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Trade::class, cascade: ['persist', 'remove'])]
    private Collection $trades;

    public function __construct()
    {
        $this->trades = new ArrayCollection();
    }

    public function getTrades(): Collection
    {
        return $this->trades;
    }

    public function addTrade(Trade $trade): self
    {
        if (!$this->trades->contains($trade)) {
            $this->trades->add($trade);
            $trade->setUser($this);
        }

        return $this;
    }

    public function removeTrade(Trade $trade): self
    {
        if ($this->trades->removeElement($trade)) {
            // Set the owning side to null (unless already changed)
            if ($trade->getUser() === $this) {
                $trade->setUser(null);
            }
        }

        return $this;
    }

    // Getters and setters for all properties including $agent
    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    // Add getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null; // Modern hashing algorithms don't need salt
    }

    public function eraseCredentials() : void
    {
        // Clear sensitive data if any
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        // Users have a default role
        return ['ROLE_USER'];
    }

    public function getLoginTime(): ?\DateTimeInterface
    {
        return $this->loginTime;
    }

    public function setLoginTime(?\DateTimeInterface $loginTime): static
    {
        $this->loginTime = $loginTime;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getTotalPnl(): ?float
    {
        return $this->totalPnl;
    }

    public function setTotalPnl(?float $totalPnl): static
    {
        $this->totalPnl = $totalPnl;

        return $this;
    }

    public function getEquity(): ?float
    {
        return $this->equity;
    }

    public function setEquity(?float $equity): static
    {
        $this->equity = $equity;

        return $this;
    }
}
