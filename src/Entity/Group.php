<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("ROLE_USER")',
            provider: ItemProvider::class,
            stateless: false
        ),
        new GetCollection(
            security: 'is_granted("ROLE_USER")',
            provider: CollectionProvider::class,
            stateless: false
        )
    ],
    normalizationContext: ['groups' => 'group:get']
)]
class Group
{
    public const FREQUENCY_WEEKLY = "W";
    public const FREQUENCY_MONTHLY = "M";

    public const STATUS_ACTIVE = "A";
    public const STATUS_INACTIVE = "I";
    public const STATUS_COMPLETED = "C";

    public const CURRENCY_USD = "USD";
    public const CURRENCY_CDF = "CDF";
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: ['group:get'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(groups: ['group:get'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['group:get'])]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(groups: ['group:get'])]
    private ?User $createdBy = null;

    #[ORM\Column(length: 20)]
    #[Groups(groups: ['group:get'])]
    private ?string $frequency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 17, scale: 2)]
    #[Groups(groups: ['group:get'])]
    private ?string $contributionAmount = null;

    #[ORM\Column(length: 3)]
    #[Groups(groups: ['group:get'])]
    private ?string $currency = null;

    #[ORM\Column(length: 1)]
    #[Groups(groups: ['group:get'])]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups(groups: ['group:get'])]
    private ?bool $deleted = null;

    #[ORM\Column]
    #[Groups(groups: ['group:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(groups: ['group:get'])]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(nullable: true)]
    #[Groups(groups: ['group:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getContributionAmount(): ?string
    {
        return $this->contributionAmount;
    }

    public function setContributionAmount(string $contributionAmount): static
    {
        $this->contributionAmount = $contributionAmount;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
