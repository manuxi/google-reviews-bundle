<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Tests\Service;

use Manuxi\GoogleReviewsBundle\Exception\ConnectionException;
use Manuxi\GoogleReviewsBundle\Service\Cache;
use Manuxi\GoogleReviewsBundle\Service\ConnectorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use stdClass;

class CacheTest extends TestCase
{
    private LoggerInterface&MockObject $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testGetWithoutCache(): void
    {
        $expectedResult = new stdClass();
        $expectedResult->reviews = [];

        $connector = $this->createMock(ConnectorInterface::class);
        $connector->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedResult);

        $this->logger->expects($this->once())
            ->method('info')
            ->with('No cache used.');

        $cache = new Cache($this->logger);
        $result = $cache->get($connector);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetWithCacheHit(): void
    {
        $expectedResult = new stdClass();
        $expectedResult->reviews = ['review1', 'review2'];

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())
            ->method('isHit')
            ->willReturn(true);
        $cacheItem->expects($this->once())
            ->method('get')
            ->willReturn($expectedResult);

        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool->expects($this->once())
            ->method('getItem')
            ->with('test-cache-key')
            ->willReturn($cacheItem);

        $connector = $this->createMock(ConnectorInterface::class);
        $connector->expects($this->once())
            ->method('getCacheKey')
            ->willReturn('test-cache-key');
        $connector->expects($this->never())
            ->method('getResult');

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Cache hit for test-cache-key.');

        $cache = new Cache($this->logger, $cachePool, 3600);
        $result = $cache->get($connector);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetWithCacheMiss(): void
    {
        $expectedResult = new stdClass();
        $expectedResult->reviews = ['review1'];

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())
            ->method('isHit')
            ->willReturn(false);
        $cacheItem->expects($this->once())
            ->method('expiresAfter')
            ->with(7200);
        $cacheItem->expects($this->once())
            ->method('set')
            ->with($expectedResult)
            ->willReturnSelf();

        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool->expects($this->once())
            ->method('getItem')
            ->with('my-cache-key')
            ->willReturn($cacheItem);
        $cachePool->expects($this->once())
            ->method('save')
            ->with($cacheItem);

        $connector = $this->createMock(ConnectorInterface::class);
        $connector->expects($this->once())
            ->method('getCacheKey')
            ->willReturn('my-cache-key');
        $connector->expects($this->once())
            ->method('hasError')
            ->willReturn(false);
        $connector->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedResult);

        $this->logger->expects($this->once())
            ->method('info')
            ->with('No cache hit for my-cache-key.');

        $cache = new Cache($this->logger, $cachePool, 7200);
        $result = $cache->get($connector);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetWithCacheMissAndConnectorError(): void
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())
            ->method('isHit')
            ->willReturn(false);

        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool->expects($this->once())
            ->method('getItem')
            ->willReturn($cacheItem);

        $connector = $this->createMock(ConnectorInterface::class);
        $connector->expects($this->once())
            ->method('getCacheKey')
            ->willReturn('error-key');
        $connector->expects($this->once())
            ->method('hasError')
            ->willReturn(true);
        $connector->expects($this->never())
            ->method('getResult');

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Failed to fetch data from google...');

        $cache = new Cache($this->logger, $cachePool, 3600);

        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('Failed to fetch data from google...');

        $cache->get($connector);
    }

    public function testConstructorWithNullCache(): void
    {
        $cache = new Cache($this->logger, null, 0);

        $expectedResult = new stdClass();
        $expectedResult->reviews = [];

        $connector = $this->createMock(ConnectorInterface::class);
        $connector->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedResult);

        $result = $cache->get($connector);
        $this->assertSame($expectedResult, $result);
    }

    public function testConstructorWithZeroTtl(): void
    {
        $expectedResult = new stdClass();
        $expectedResult->reviews = [];

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('isHit')->willReturn(false);
        $cacheItem->expects($this->once())
            ->method('expiresAfter')
            ->with(0);
        $cacheItem->method('set')->willReturnSelf();

        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool->method('getItem')->willReturn($cacheItem);
        $cachePool->method('save');

        $connector = $this->createMock(ConnectorInterface::class);
        $connector->method('getCacheKey')->willReturn('key');
        $connector->method('hasError')->willReturn(false);
        $connector->method('getResult')->willReturn($expectedResult);

        $cache = new Cache($this->logger, $cachePool, 0);
        $cache->get($connector);
    }
}
