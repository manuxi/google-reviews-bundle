<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Tests\Model;

use Manuxi\GoogleReviewsBundle\Model\Review;
use PHPUnit\Framework\TestCase;

class ReviewTest extends TestCase
{
    private Review $review;

    protected function setUp(): void
    {
        $this->review = new Review();
    }

    public function testDefaultValues(): void
    {
        $this->assertSame('', $this->review->getAuthorName());
        $this->assertSame('', $this->review->getAuthorUrl());
        $this->assertSame('', $this->review->getLanguage());
        $this->assertSame('', $this->review->getProfilePhotoUrl());
        $this->assertSame(0, $this->review->getRating());
        $this->assertSame('', $this->review->getRelativeTimeDescription());
        $this->assertSame('', $this->review->getText());
        $this->assertSame(0, $this->review->getTime());
    }

    public function testSetAndGetAuthorName(): void
    {
        $this->review->setAuthorName('John Doe');
        $this->assertSame('John Doe', $this->review->getAuthorName());
    }

    public function testSetAndGetAuthorUrl(): void
    {
        $url = 'https://www.google.com/maps/contrib/123/reviews';
        $this->review->setAuthorUrl($url);
        $this->assertSame($url, $this->review->getAuthorUrl());
    }

    public function testSetAndGetLanguage(): void
    {
        $this->review->setLanguage('de-DE');
        $this->assertSame('de-DE', $this->review->getLanguage());
    }

    public function testSetAndGetProfilePhotoUrl(): void
    {
        $url = 'https://googleusercontent.com/photo.jpg';
        $this->review->setProfilePhotoUrl($url);
        $this->assertSame($url, $this->review->getProfilePhotoUrl());
    }

    public function testSetAndGetRating(): void
    {
        $this->review->setRating(5);
        $this->assertSame(5, $this->review->getRating());
    }

    public function testSetAndGetRatingBoundaries(): void
    {
        $this->review->setRating(1);
        $this->assertSame(1, $this->review->getRating());

        $this->review->setRating(5);
        $this->assertSame(5, $this->review->getRating());
    }

    public function testSetAndGetRelativeTimeDescription(): void
    {
        $this->review->setRelativeTimeDescription('2 weeks ago');
        $this->assertSame('2 weeks ago', $this->review->getRelativeTimeDescription());
    }

    public function testSetAndGetText(): void
    {
        $text = 'This is a great review with special characters: äöü €';
        $this->review->setText($text);
        $this->assertSame($text, $this->review->getText());
    }

    public function testSetAndGetTime(): void
    {
        $timestamp = 1614556923;
        $this->review->setTime($timestamp);
        $this->assertSame($timestamp, $this->review->getTime());
    }

    public function testFullReview(): void
    {
        $this->review->setAuthorName('Jane Smith');
        $this->review->setAuthorUrl('https://www.google.com/maps/contrib/456/reviews');
        $this->review->setLanguage('en-US');
        $this->review->setProfilePhotoUrl('https://example.com/photo.jpg');
        $this->review->setRating(4);
        $this->review->setRelativeTimeDescription('3 days ago');
        $this->review->setText('Excellent service!');
        $this->review->setTime(1700000000);

        $this->assertSame('Jane Smith', $this->review->getAuthorName());
        $this->assertSame('https://www.google.com/maps/contrib/456/reviews', $this->review->getAuthorUrl());
        $this->assertSame('en-US', $this->review->getLanguage());
        $this->assertSame('https://example.com/photo.jpg', $this->review->getProfilePhotoUrl());
        $this->assertSame(4, $this->review->getRating());
        $this->assertSame('3 days ago', $this->review->getRelativeTimeDescription());
        $this->assertSame('Excellent service!', $this->review->getText());
        $this->assertSame(1700000000, $this->review->getTime());
    }

    public function testEmptyText(): void
    {
        $this->review->setText('');
        $this->assertSame('', $this->review->getText());
    }

    public function testLongText(): void
    {
        $longText = str_repeat('This is a long review. ', 100);
        $this->review->setText($longText);
        $this->assertSame($longText, $this->review->getText());
    }
}
