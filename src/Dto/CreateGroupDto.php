<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateGroupDto
{   
    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?string $name = null;
    
    public ?string $description = null;
    
    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?string $frequency = null;
    
    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?string $contributionAmount = null;
    
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Currency]
    public ?string $currency = null;
    
    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?\DateTimeImmutable $startDate = null;
}