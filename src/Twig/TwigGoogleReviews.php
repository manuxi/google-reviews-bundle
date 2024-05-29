<?php

namespace Manuxi\GoogleReviewsBundle\Twig;

use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviews;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigGoogleReviews extends AbstractExtension
{
    private $googleReviews;

    public function __construct(ManuxiGoogleReviews $googleReviews)
    {
        $this->googleReviews = $googleReviews;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_google_reviews', [$this, 'getReviews']),
            new TwigFunction('get_google_reviews_count', [$this, 'getReviewsCount']),
        ];
    }

    public function getReviews(int $offset = 0, int $length = 0): array
    {
        return $this->googleReviews->getReviews($offset, $length);
    }

    public function getReviewsCount(): int
    {
        return $this->googleReviews->getReviewsCount();
    }
}
