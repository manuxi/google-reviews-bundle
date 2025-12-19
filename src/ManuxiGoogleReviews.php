<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle;

use Manuxi\GoogleReviewsBundle\Exception\ConnectionException;
use Manuxi\GoogleReviewsBundle\Model\Review;
use Manuxi\GoogleReviewsBundle\Service\Cache;
use Manuxi\GoogleReviewsBundle\Service\ConnectorInterface;
use Psr\Cache\InvalidArgumentException;
use stdClass;
use Symfony\Component\Serializer\SerializerInterface;

class ManuxiGoogleReviews
{
    private ?stdClass $result = null;

    public function __construct(
        private readonly ConnectorInterface $connector,
        private readonly Cache $cache,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws ConnectionException|InvalidArgumentException
     */
    private function getCachedResult(): void
    {
        if (null === $this->result) {
            $this->result = $this->cache->get($this->connector);
        }
    }

    /**
     * @return Review[]
     *
     * @throws ConnectionException|InvalidArgumentException
     */
    public function getReviews(int $offset = 0, int $length = 0): array
    {
        $this->getCachedResult();

        $reviews = [];
        foreach ($this->result->reviews as $review) {
            $reviews[] = $this->serializer->deserialize(\json_encode($review), Review::class, 'json');
        }

        if (0 !== $offset || 0 !== $length) {
            $reviews = \array_slice($reviews, $offset, $length ?: null);
        }

        return $reviews;
    }

    /**
     * @throws ConnectionException|InvalidArgumentException
     */
    public function getReviewsCount(): int
    {
        $this->getCachedResult();

        return \count($this->result->reviews);
    }
}
