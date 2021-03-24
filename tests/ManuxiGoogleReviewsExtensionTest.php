<?php

namespace Manuxi\GoogleReviewsBundle\Tests;

use Manuxi\GoogleReviewsBundle\DependencyInjection\ManuxiGoogleReviewsExtension;
use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviews;
use Manuxi\GoogleReviewsBundle\Service\Cache;
use Manuxi\GoogleReviewsBundle\Service\CurlConnector;
use Manuxi\GoogleReviewsBundle\Twig\TwigGoogleReviews;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class ManuxiGoogleReviewsExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new ManuxiGoogleReviewsExtension(),
        ];
    }

    public function testServiceExists()
    {
        $this->load();
        $this->assertContainerBuilderHasService('manuxi_google_reviews.google_reviews');
        $this->assertContainerBuilderHasAlias(ManuxiGoogleReviews::class, 'manuxi_google_reviews.google_reviews');
        $this->assertContainerBuilderHasService('manuxi_google_reviews.connector', CurlConnector::class);
        $this->assertContainerBuilderHasService('manuxi_google_reviews.cache', Cache::class);
        $this->assertContainerBuilderHasService('twig.extension.manuxi_google_reviews', TwigGoogleReviews::class);

    }
}
