<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Tests;

use Manuxi\GoogleReviewsBundle\Exception\ConnectionException;
use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviews;
use Manuxi\GoogleReviewsBundle\Model\Review;
use Manuxi\GoogleReviewsBundle\Service\Cache;
use Manuxi\GoogleReviewsBundle\Service\CurlConnector;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FunctionalTest extends TestCase
{
    private static string $responseSuccess;
    private Serializer $serializer;
    private Cache $cache;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$responseSuccess = \file_get_contents(__DIR__ . '/config/success.json');
    }

    protected function setUp(): void
    {
        $this->serializer = new Serializer(
            [new ObjectNormalizer()],
            [new JsonEncoder()]
        );
        $this->cache = new Cache(new NullLogger());
    }

    public function testSuccessfulReturn(): void
    {
        $curlConnectorMock = $this->createMock(CurlConnector::class);
        $curlConnectorMock->expects($this->any())
            ->method('getResult')
            ->willReturn(\json_decode(self::$responseSuccess)->result);

        $googleReviews = new ManuxiGoogleReviews($curlConnectorMock, $this->cache, $this->serializer);

        $this->assertSame(6, $googleReviews->getReviewsCount());
        $this->assertSame(6, \count($googleReviews->getReviews()));
        $this->assertSame(1, \count($googleReviews->getReviews(0, 1)));
        $this->assertSame(2, \count($googleReviews->getReviews(2, 2)));
        $this->assertSame(1, \count($googleReviews->getReviews(5, 2)));
        $this->assertSame(0, \count($googleReviews->getReviews(6, 2)));

        /** @var Review $review */
        $review = $googleReviews->getReviews()[0];
        $this->assertInstanceOf(Review::class, $review);
        $this->assertSame('Cassandra R Collier', $review->getAuthorName());
    }

    public function testReviewDataMapping(): void
    {
        $curlConnectorMock = $this->createMock(CurlConnector::class);
        $curlConnectorMock->method('getResult')
            ->willReturn(\json_decode(self::$responseSuccess)->result);

        $googleReviews = new ManuxiGoogleReviews($curlConnectorMock, $this->cache, $this->serializer);

        /** @var Review $review */
        $review = $googleReviews->getReviews()[0];

        $this->assertSame('Cassandra R Collier', $review->getAuthorName());
        $this->assertSame('https://www.google.com/maps/contrib/123123123/reviews', $review->getAuthorUrl());
        $this->assertSame('en-US', $review->getLanguage());
        $this->assertSame('https://googleusercontent.com/photo.jpg', $review->getProfilePhotoUrl());
        $this->assertSame(5, $review->getRating());
        $this->assertSame('1 year ago', $review->getRelativeTimeDescription());
        $this->assertSame('Thank you for the excellent work. Just great!', $review->getText());
        $this->assertSame(1583020923, $review->getTime());
    }

    public function testSecondReviewDataMapping(): void
    {
        $curlConnectorMock = $this->createMock(CurlConnector::class);
        $curlConnectorMock->method('getResult')
            ->willReturn(\json_decode(self::$responseSuccess)->result);

        $googleReviews = new ManuxiGoogleReviews($curlConnectorMock, $this->cache, $this->serializer);

        /** @var Review $review */
        $review = $googleReviews->getReviews()[1];

        $this->assertSame('Sharon A Howard', $review->getAuthorName());
        $this->assertSame(5, $review->getRating());
        $this->assertSame('Thank you very much. That was awesome. Gladly again.', $review->getText());
    }

    public function testPaginationOffset(): void
    {
        $curlConnectorMock = $this->createMock(CurlConnector::class);
        $curlConnectorMock->method('getResult')
            ->willReturn(\json_decode(self::$responseSuccess)->result);

        $googleReviews = new ManuxiGoogleReviews($curlConnectorMock, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews(1, 2);

        $this->assertCount(2, $reviews);
        $this->assertSame('Sharon A Howard', $reviews[0]->getAuthorName());
    }

    public function testPaginationWithLargeOffset(): void
    {
        $curlConnectorMock = $this->createMock(CurlConnector::class);
        $curlConnectorMock->method('getResult')
            ->willReturn(\json_decode(self::$responseSuccess)->result);

        $googleReviews = new ManuxiGoogleReviews($curlConnectorMock, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews(100, 10);

        $this->assertCount(0, $reviews);
    }

    public function testAllReviewsAreReviewInstances(): void
    {
        $curlConnectorMock = $this->createMock(CurlConnector::class);
        $curlConnectorMock->method('getResult')
            ->willReturn(\json_decode(self::$responseSuccess)->result);

        $googleReviews = new ManuxiGoogleReviews($curlConnectorMock, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews();

        foreach ($reviews as $review) {
            $this->assertInstanceOf(Review::class, $review);
        }
    }

    public function testBadCredentials(): void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('The provided API key is invalid.');

        $curlConnector = new CurlConnector('123', '123');

        $googleReviews = new ManuxiGoogleReviews($curlConnector, $this->cache, $this->serializer);
        $googleReviews->getReviewsCount();
    }

    public function testReviewCountMatchesReviewsLength(): void
    {
        $curlConnectorMock = $this->createMock(CurlConnector::class);
        $curlConnectorMock->method('getResult')
            ->willReturn(\json_decode(self::$responseSuccess)->result);

        $googleReviews = new ManuxiGoogleReviews($curlConnectorMock, $this->cache, $this->serializer);

        $count = $googleReviews->getReviewsCount();
        $reviews = $googleReviews->getReviews();

        $this->assertSame($count, \count($reviews));
    }

    public function testResponseIsCached(): void
    {
        $curlConnectorMock = $this->createMock(CurlConnector::class);
        $curlConnectorMock->expects($this->once())
            ->method('getResult')
            ->willReturn(\json_decode(self::$responseSuccess)->result);

        $googleReviews = new ManuxiGoogleReviews($curlConnectorMock, $this->cache, $this->serializer);

        $googleReviews->getReviews();
        $googleReviews->getReviews();
        $googleReviews->getReviewsCount();
        $googleReviews->getReviews(0, 1);
    }
}
