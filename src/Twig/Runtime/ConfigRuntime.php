<?php

namespace App\Twig\Runtime;

use App\Repository\ConfigRepository;
use Twig\Extension\RuntimeExtensionInterface;

class ConfigRuntime implements RuntimeExtensionInterface
{
    private $repo;
    public function __construct(ConfigRepository $repo)
    {
        $this->repo = $repo;
    }

    public function googleAdsence()
    {
        return $this->repo->findOneBy([])->getGoogleAdsence();
    }
    public function googleAnalytics() 
    {
        return $this->repo->findOneBy([])->getGoogleAnalytics();
    }
}
