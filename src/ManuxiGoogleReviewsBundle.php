<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle;

use Manuxi\GoogleReviewsBundle\DependencyInjection\ManuxiGoogleReviewsExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ManuxiGoogleReviewsBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return $this->extension ??= new ManuxiGoogleReviewsExtension();
    }
}
