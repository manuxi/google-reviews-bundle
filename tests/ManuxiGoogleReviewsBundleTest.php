<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Tests;

use Manuxi\GoogleReviewsBundle\DependencyInjection\ManuxiGoogleReviewsExtension;
use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviewsBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ManuxiGoogleReviewsBundleTest extends TestCase
{
    private ManuxiGoogleReviewsBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new ManuxiGoogleReviewsBundle();
    }

    public function testExtendsBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testGetContainerExtensionReturnsExtension(): void
    {
        $extension = $this->bundle->getContainerExtension();

        $this->assertInstanceOf(ExtensionInterface::class, $extension);
        $this->assertInstanceOf(ManuxiGoogleReviewsExtension::class, $extension);
    }

    public function testGetContainerExtensionReturnsSameInstance(): void
    {
        $extension1 = $this->bundle->getContainerExtension();
        $extension2 = $this->bundle->getContainerExtension();

        $this->assertSame($extension1, $extension2);
    }

    public function testExtensionAlias(): void
    {
        $extension = $this->bundle->getContainerExtension();

        $this->assertSame('manuxi_google_reviews', $extension->getAlias());
    }
}
