<?php

namespace App\Entity;

use App\Repository\ZohoCRMRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity(repositoryClass: ZohoCRMRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ZohoCRM
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'zohoCRM')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\Column(length: 255)]
    private ?string $apiToken = null;

    #[ORM\Column(length: 255)]
    private ?string $refreshToken = null;

    #[ORM\Column(length: 255)]
    private ?string $clientId = null;

    #[ORM\Column(length: 255)]
    private ?string $clientSecret = null;

    #[ORM\Column]
    private ?bool $isActive = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $lastSynced = null;
    
    #[ORM\Column]
    private ?\DateTime $createdAt = null;
    
    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): static
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getLastSynced(): ?\DateTime
    {
        return $this->lastSynced;
    }

    public function setLastSynced(?\DateTime $lastSynced): static
    {
        $this->lastSynced = $lastSynced;

        return $this;
    }
    
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
    
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }
    
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
