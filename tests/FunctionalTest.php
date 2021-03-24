<?php

namespace Manuxi\GoogleReviewsBundle\Tests;

use Manuxi\GoogleReviewsBundle\Exception\ConnectionException;
use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviews;
use Manuxi\GoogleReviewsBundle\Model\Review;
use Manuxi\GoogleReviewsBundle\Service\Cache;
use Manuxi\GoogleReviewsBundle\Service\CurlConnector;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FunctionalTest extends TestCase
{
    private static $responseSuccess;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$responseSuccess = \file_get_contents(__DIR__ . '/config/success.json');
    }

    public function testSuccessfulReturn()
    {
        $curlConnectorMock = $this->createMock(CurlConnector::class);

        $curlConnectorMock->expects($this->any())
            ->method('getResult')
            ->willReturn(\json_decode(self::$responseSuccess)->result);

        $loggerMock = $this->createMock(Logger::class);
        $cache      = new Cache($loggerMock);

        $normalizers = [new ObjectNormalizer()];
        $encoders    = [new JsonEncoder()];
        $serializer  = new Serializer($normalizers, $encoders);

        $googleReviews = new ManuxiGoogleReviews($curlConnectorMock, $cache, $serializer);
        $this->assertSame(6, $googleReviews->getReviewsCount());
        $this->assertSame(6, \count($googleReviews->getReviews()));
        $this->assertSame(1, \count($googleReviews->getReviews(0, 1)));
        $this->assertSame(2, \count($googleReviews->getReviews(2, 2)));
        $this->assertSame(1, \count($googleReviews->getReviews(5, 2)));
        $this->assertSame(0, \count($googleReviews->getReviews(6, 2)));

        /** @var Review $review */
        $review = $googleReviews->getReviews()[0];
        $this->assertInstanceOf(Review::class, $review);
        $this->assertSame($review->getAuthorName(), 'Cassandra R Collier');

    }

    public function testBadCredentials()
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('The provided API key is invalid.');

        $curlConnector = new CurlConnector(123, 123);

        $loggerMock = $this->createMock(Logger::class);
        $cache      = new Cache($loggerMock);

        $normalizers = [new ObjectNormalizer()];
        $encoders    = [new JsonEncoder()];
        $serializer  = new Serializer($normalizers, $encoders);

        $googleReviews = new ManuxiGoogleReviews($curlConnector, $cache, $serializer);
        $googleReviews->getReviewsCount();

    }
}
