<?php

namespace manuxi\GoogleBusinessDataBundle;

use manuxi\GoogleBusinessDataBundle\Service\Cache;
use manuxi\GoogleBusinessDataBundle\Service\Connector;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class ManuxiGoogleBusinessData
{
    private $cache;

    private $connector;

    public function __construct(Connector $connector, Cache $cache)
    {
        $this->connector = $connector;
        $this->cache     = $cache;
    }

    public function getResultSet()
    {
        return $this->cache->get($this->connector);
    }

}
