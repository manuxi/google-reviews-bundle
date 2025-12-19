<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Service;

use Manuxi\GoogleReviewsBundle\Exception\ConnectionException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use stdClass;

class Cache
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ?CacheItemPoolInterface $cache = null,
        private readonly int $ttl = 0,
    ) {
    }

    /**
     * @throws ConnectionException|InvalidArgumentException
     */
    public function get(ConnectorInterface $connector): stdClass
    {
        if (null !== $this->cache) {
            $cacheKey = $connector->getCacheKey();
            $cacheItem = $this->cache->getItem($cacheKey);

            if (!$cacheItem->isHit()) {
                if ($connector->hasError()) {
                    $error = 'Failed to fetch data from google...';
                    $this->logger->error($error);

                    throw new ConnectionException($error);
                }

                $this->logger->info(\sprintf('No cache hit for %s.', $cacheKey));
                $cacheItem->expiresAfter($this->ttl);
                $result = $connector->getResult();
                $this->cache->save($cacheItem->set($result));
            } else {
                $this->logger->info(\sprintf('Cache hit for %s.', $cacheKey));
                $result = $cacheItem->get();
            }

            return $result;
        }

        $this->logger->info('No cache used.');

        return $connector->getResult();
    }
}
