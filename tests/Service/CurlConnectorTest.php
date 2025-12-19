<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Tests\Service;

use Manuxi\GoogleReviewsBundle\Service\ConnectorInterface;
use Manuxi\GoogleReviewsBundle\Service\CurlConnector;
use PHPUnit\Framework\TestCase;

class CurlConnectorTest extends TestCase
{
    public function testImplementsConnectorInterface(): void
    {
        $connector = new CurlConnector('test-cid', 'test-api-key');
        $this->assertInstanceOf(ConnectorInterface::class, $connector);
    }

    public function testGetCacheKey(): void
    {
        $cid = 'my-cid-123';
        $apiKey = 'my-api-key-456';
        $connector = new CurlConnector($cid, $apiKey);

        $this->assertSame($cid . $apiKey, $connector->getCacheKey());
    }

    public function testGetCacheKeyWithDifferentCredentials(): void
    {
        $connector1 = new CurlConnector('cid1', 'key1');
        $connector2 = new CurlConnector('cid2', 'key2');

        $this->assertNotSame($connector1->getCacheKey(), $connector2->getCacheKey());
    }

    public function testStatusOkConstant(): void
    {
        $this->assertSame('OK', CurlConnector::STATUS_OK);
    }

    public function testConstructorWithDefaultLocale(): void
    {
        $connector = new CurlConnector('cid', 'key');
        $cacheKey = $connector->getCacheKey();

        $this->assertSame('cidkey', $cacheKey);
    }

    public function testConstructorWithCustomLocale(): void
    {
        $connector = new CurlConnector('cid', 'key', 'de');
        $cacheKey = $connector->getCacheKey();

        $this->assertSame('cidkey', $cacheKey);
    }
}
