<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Twig;

use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviews;
use Manuxi\GoogleReviewsBundle\Model\Review;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigGoogleReviews extends AbstractExtension
{
    public function __construct(
        private readonly ManuxiGoogleReviews $googleReviews,
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_google_reviews', $this->getReviews(...)),
            new TwigFunction('get_google_reviews_count', $this->getReviewsCount(...)),
        ];
    }

    /**
     * @return Review[]
     */
    public function getReviews(int $offset = 0, int $length = 0): array
    {
        return $this->googleReviews->getReviews($offset, $length);
    }

    public function getReviewsCount(): int
    {
        return $this->googleReviews->getReviewsCount();
    }
}
