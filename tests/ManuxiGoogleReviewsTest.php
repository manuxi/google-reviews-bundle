<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Tests;

use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviews;
use Manuxi\GoogleReviewsBundle\Model\Review;
use Manuxi\GoogleReviewsBundle\Service\Cache;
use Manuxi\GoogleReviewsBundle\Service\ConnectorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use stdClass;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ManuxiGoogleReviewsTest extends TestCase
{
    private ConnectorInterface&MockObject $connector;
    private Cache $cache;
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->connector = $this->createMock(ConnectorInterface::class);
        $this->cache = new Cache(new NullLogger());
        $this->serializer = new Serializer(
            [new ObjectNormalizer()],
            [new JsonEncoder()]
        );
    }

    public function testGetReviewsReturnsArray(): void
    {
        $result = $this->createResultWithReviews(2);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews();

        $this->assertIsArray($reviews);
        $this->assertCount(2, $reviews);
    }

    public function testGetReviewsReturnsReviewInstances(): void
    {
        $result = $this->createResultWithReviews(1);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews();

        $this->assertInstanceOf(Review::class, $reviews[0]);
    }

    public function testGetReviewsWithOffsetOnly(): void
    {
        $result = $this->createResultWithReviews(5);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        // offset=2, length=0 means: get all from offset 2 to end
        $reviews = $googleReviews->getReviews(2, 0);

        $this->assertCount(3, $reviews);
    }

    public function testGetReviewsWithZeroOffsetAndZeroLength(): void
    {
        $result = $this->createResultWithReviews(5);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        // Both 0 means: get all reviews
        $reviews = $googleReviews->getReviews(0, 0);

        $this->assertCount(5, $reviews);
    }

    public function testGetReviewsWithLength(): void
    {
        $result = $this->createResultWithReviews(5);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews(0, 2);

        $this->assertCount(2, $reviews);
    }

    public function testGetReviewsWithOffsetAndLength(): void
    {
        $result = $this->createResultWithReviews(10);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews(3, 4);

        $this->assertCount(4, $reviews);
    }

    public function testGetReviewsWithOffsetBeyondCount(): void
    {
        $result = $this->createResultWithReviews(3);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews(10, 5);

        $this->assertCount(0, $reviews);
    }

    public function testGetReviewsWithLengthBeyondAvailable(): void
    {
        $result = $this->createResultWithReviews(3);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews(1, 10);

        $this->assertCount(2, $reviews);
    }

    public function testGetReviewsCount(): void
    {
        $result = $this->createResultWithReviews(7);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $count = $googleReviews->getReviewsCount();

        $this->assertSame(7, $count);
    }

    public function testGetReviewsCountWithZeroReviews(): void
    {
        $result = $this->createResultWithReviews(0);
        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $count = $googleReviews->getReviewsCount();

        $this->assertSame(0, $count);
    }

    public function testResultIsCached(): void
    {
        $result = $this->createResultWithReviews(3);
        $this->connector->expects($this->once())
            ->method('getResult')
            ->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $googleReviews->getReviews();
        $googleReviews->getReviews();
        $googleReviews->getReviewsCount();
    }

    public function testReviewDataIsCorrectlyDeserialized(): void
    {
        $result = new stdClass();
        $result->reviews = [
            (object) [
                'author_name' => 'John Doe',
                'author_url' => 'https://example.com/john',
                'language' => 'en-US',
                'profile_photo_url' => 'https://example.com/photo.jpg',
                'rating' => 5,
                'relative_time_description' => '2 weeks ago',
                'text' => 'Great service!',
                'time' => 1700000000,
            ],
        ];

        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews();

        $this->assertCount(1, $reviews);

        /** @var Review $review */
        $review = $reviews[0];

        $this->assertSame('John Doe', $review->getAuthorName());
        $this->assertSame('https://example.com/john', $review->getAuthorUrl());
        $this->assertSame('en-US', $review->getLanguage());
        $this->assertSame('https://example.com/photo.jpg', $review->getProfilePhotoUrl());
        $this->assertSame(5, $review->getRating());
        $this->assertSame('2 weeks ago', $review->getRelativeTimeDescription());
        $this->assertSame('Great service!', $review->getText());
        $this->assertSame(1700000000, $review->getTime());
    }

    public function testMultipleReviewsAreCorrectlyDeserialized(): void
    {
        $result = new stdClass();
        $result->reviews = [
            (object) [
                'author_name' => 'Author 1',
                'author_url' => '',
                'language' => 'en',
                'profile_photo_url' => '',
                'rating' => 5,
                'relative_time_description' => '',
                'text' => 'Review 1',
                'time' => 1000000000,
            ],
            (object) [
                'author_name' => 'Author 2',
                'author_url' => '',
                'language' => 'de',
                'profile_photo_url' => '',
                'rating' => 4,
                'relative_time_description' => '',
                'text' => 'Review 2',
                'time' => 2000000000,
            ],
        ];

        $this->connector->method('getResult')->willReturn($result);

        $googleReviews = new ManuxiGoogleReviews($this->connector, $this->cache, $this->serializer);

        $reviews = $googleReviews->getReviews();

        $this->assertCount(2, $reviews);
        $this->assertSame('Author 1', $reviews[0]->getAuthorName());
        $this->assertSame('Author 2', $reviews[1]->getAuthorName());
        $this->assertSame(5, $reviews[0]->getRating());
        $this->assertSame(4, $reviews[1]->getRating());
    }

    private function createResultWithReviews(int $count): stdClass
    {
        $result = new stdClass();
        $result->reviews = [];

        for ($i = 0; $i < $count; ++$i) {
            $result->reviews[] = (object) [
                'author_name' => 'Author ' . $i,
                'author_url' => 'https://example.com/' . $i,
                'language' => 'en',
                'profile_photo_url' => 'https://example.com/photo' . $i . '.jpg',
                'rating' => ($i % 5) + 1,
                'relative_time_description' => $i . ' days ago',
                'text' => 'Review text ' . $i,
                'time' => 1700000000 + $i,
            ];
        }

        return $result;
    }
}
