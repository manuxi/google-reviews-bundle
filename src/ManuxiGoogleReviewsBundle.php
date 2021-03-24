<?php

namespace Manuxi\GoogleReviewsBundle;

use Manuxi\GoogleReviewsBundle\DependencyInjection\ManuxiGoogleReviewsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ManuxiGoogleReviewsBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ManuxiGoogleReviewsExtension();
        }
        return $this->extension;
    }
}
