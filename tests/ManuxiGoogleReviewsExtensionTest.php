<?php

declare(strict_types=1);

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

    protected function getMinimalConfiguration(): array
    {
        return [
            'connector' => [
                'cid' => 'test-cid',
                'api_key' => 'test-api-key',
            ],
        ];
    }

    public function testServiceExists(): void
    {
        $this->load($this->getMinimalConfiguration());
        $this->assertContainerBuilderHasService('manuxi_google_reviews.google_reviews');
        $this->assertContainerBuilderHasAlias(ManuxiGoogleReviews::class, 'manuxi_google_reviews.google_reviews');
        $this->assertContainerBuilderHasService('manuxi_google_reviews.connector', CurlConnector::class);
        $this->assertContainerBuilderHasService('manuxi_google_reviews.cache', Cache::class);
        $this->assertContainerBuilderHasService('twig.extension.manuxi_google_reviews', TwigGoogleReviews::class);
    }

    public function testConnectorArguments(): void
    {
        $this->load([
            'connector' => [
                'cid' => 'my-business-cid',
                'api_key' => 'my-secret-key',
                'locale' => 'de',
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'manuxi_google_reviews.connector',
            0,
            'my-business-cid'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'manuxi_google_reviews.connector',
            1,
            'my-secret-key'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'manuxi_google_reviews.connector',
            2,
            'de'
        );
    }

    public function testCacheDisabled(): void
    {
        $this->load([
            'connector' => [
                'cid' => 'test-cid',
                'api_key' => 'test-key',
            ],
            'cache' => [
                'enabled' => false,
            ],
        ]);

        $this->assertContainerBuilderHasService('manuxi_google_reviews.cache', Cache::class);
    }

    public function testCacheEnabled(): void
    {
        $this->load([
            'connector' => [
                'cid' => 'test-cid',
                'api_key' => 'test-key',
            ],
            'cache' => [
                'enabled' => true,
                'pool' => 'cache.app',
                'ttl' => 7200,
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'manuxi_google_reviews.cache',
            2,
            7200
        );
    }
}
