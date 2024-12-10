<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity]
#[ORM\Table(name: 'agent')]
class Agent implements UserInterface, PasswordAuthenticatedUserInterface
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
    private string $role;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subAgents')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?self $supervisor = null;

    #[ORM\OneToMany(mappedBy: 'agent', targetEntity: User::class)]
    private $users;

    #[ORM\OneToMany(mappedBy: 'supervisor', targetEntity: self::class)]
    private $subAgents;

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subAgents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    // Getters and setters for all properties including $users and $supervisor

    public function getSupervisor(): ?self
    {
        return $this->supervisor;
    }

    public function setSupervisor(?self $supervisor): self
    {
        $this->supervisor = $supervisor;

        return $this;
    }

    public function eraseCredentials() : void
    {
        // Clear sensitive data if any
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setAgent($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            if ($user->getAgent() === $this) {
                $user->setAgent(null);
            }
        }

        return $this;
    }

    public function getSubAgents()
    {
        return $this->subAgents;
    }

    public function addSubAgent(self $agent): self
    {
        if (!$this->subAgents->contains($agent)) {
            $this->subAgents[] = $agent;
            $agent->setSupervisor($this);
        }

        return $this;
    }

    public function removeSubAgent(self $agent): self
    {
        if ($this->subAgents->removeElement($agent)) {
            if ($agent->getSupervisor() === $this) {
                $agent->setSupervisor(null);
            }
        }

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

    public function getLoginTime(): ?\DateTimeInterface
    {
        return $this->loginTime;
    }

    public function setLoginTime(?\DateTimeInterface $loginTime): static
    {
        $this->loginTime = $loginTime;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }
    public function getRoles(): array
    {
        // Users have a default role
        return [$this->role];
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getAssignedUsers(Agent $agent): array
    {
        $hierarchy = [
            'agent' => $agent,
            'subAgents' => [],
            'assignedUsers' => $agent->getUsers(),
        ];

        foreach ($agent->getSubAgents() as $subAgent) {
            $hierarchy['subAgents'][] = $this->getAssignedUsers($subAgent);
        }

        return $hierarchy;
    }
}
