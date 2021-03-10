<?php

namespace manuxi\GoogleBusinessDataBundle\Service;

use manuxi\GoogleBusinessDataBundle\Exception\ConnectionException;
use stdClass;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Cache
{
    /**
     * @var AdapterInterface|null
     */
    private $cache;
    /**
     * @var int
     */
    private $lifetime;

    public function __construct(AdapterInterface $cache = null, $lifetime = 0)
    {
        $this->cache    = $cache;
        $this->lifetime = $lifetime;
    }

    /**
     * @param Connector $connector
     * @return stdClass
     * @throws ConnectionException
     */
    public function get(Connector $connector): stdClass
    {
        if (null !== $this->cache) {
            return $this->cache->get($connector->getCacheKey(), function (ItemInterface $item) use ($connector) {
                $item->expiresAfter($this->lifetime);
                if ($connector->hasError()) {
                    throw new ConnectionException('Failed to fetch data from google');
                }
                return $connector->getResult();
            });
        }
        return $connector->getResult();
    }
}
