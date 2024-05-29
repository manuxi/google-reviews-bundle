<?php

namespace Manuxi\GoogleReviewsBundle;

use Manuxi\GoogleReviewsBundle\Model\Review;
use Manuxi\GoogleReviewsBundle\Service\Cache;
use Manuxi\GoogleReviewsBundle\Service\ConnectorInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Serializer\SerializerInterface;

class ManuxiGoogleReviews
{
    private $cache;
    private $connector;
    private $serializer;
    private $result;

    public function __construct(ConnectorInterface $connector, Cache $cache, SerializerInterface $serializer)
    {
        $this->connector  = $connector;
        $this->cache      = $cache;
        $this->serializer = $serializer;
    }

    /**
     * @throws Exception\ConnectionException|InvalidArgumentException
     */
    private function getCachedResult(): void
    {
        if (null === $this->result) {
            $this->result = $this->cache->get($this->connector);
        }
    }

    public function getReviews(int $offset = 0, int $length = 0): array
    {
        $this->getCachedResult();

        $reviews = [];
        foreach ($this->result->reviews as $review) {
            $reviews[] = $this->serializer->deserialize(\json_encode($review), Review::class, 'json');
        }

        if (0 !== $offset || 0 !== $length) {
            $reviews = \array_slice($reviews, $offset, $length);
        }

        return $reviews;
    }

    public function getReviewsCount(): int
    {
        $this->getCachedResult();

        return \count($this->result->reviews);
    }
}
