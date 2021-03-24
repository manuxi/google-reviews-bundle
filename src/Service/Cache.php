<?php

namespace Manuxi\GoogleReviewsBundle\Service;

use Manuxi\GoogleReviewsBundle\Exception\ConnectionException;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Cache
{
    /**
     * @var AdapterInterface|null
     */
    private $cache;
    /**
     * @var int
     */
    private $ttl;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, AdapterInterface $cache = null, $ttl = 0)
    {
        $this->cache  = $cache;
        $this->ttl    = $ttl;
        $this->logger = $logger;
    }

    /**
     * @throws ConnectionException|InvalidArgumentException
     * @return stdClass
     */
    public function get(ConnectorInterface $connector)
    {
        if (null !== $this->cache) {
            $cacheKey  = $connector->getCacheKey();
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

//            return $this->cache->get($connector->getCacheKey(), function (ItemInterface $item) use ($connector) {
//                if ($connector->hasError()) {
//                    $error = 'Failed to fetch data from google';
//                    $this->logger->error($error);
//                    throw new ConnectionException($error);
//                }
//                $this->logger->info(\sprintf('No cache hit for %s.', $connector->getCacheKey()));
//                $item->expiresAfter($this->ttl);
//                return $connector->getResult();
//            });
        }
        $this->logger->info('No cache used.');
        return $connector->getResult();
    }
}
