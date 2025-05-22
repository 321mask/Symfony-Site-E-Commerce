<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
class Config
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $googleAnalytics = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $googleAdsence = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoogleAnalytics(): ?string
    {
        return $this->googleAnalytics;
    }

    public function setGoogleAnalytics(?string $googleAnalytics): static
    {
        $this->googleAnalytics = $googleAnalytics;

        return $this;
    }

    public function getGoogleAdsence(): ?string
    {
        return $this->googleAdsence;
    }

    public function setGoogleAdsence(?string $googleAdsence): static
    {
        $this->googleAdsence = $googleAdsence;

        return $this;
    }
}
