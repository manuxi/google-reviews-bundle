<?php

namespace manuxi\GoogleBusinessDataBundle;

use manuxi\GoogleBusinessDataBundle\DependencyInjection\ManuxiGoogleBusinessDataExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ManuxiGoogleBusinessDataBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ManuxiGoogleBusinessDataExtension();
        }
        return $this->extension;
    }
}
