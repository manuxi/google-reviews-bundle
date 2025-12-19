<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Tests\Twig;

use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviews;
use Manuxi\GoogleReviewsBundle\Model\Review;
use Manuxi\GoogleReviewsBundle\Twig\TwigGoogleReviews;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigGoogleReviewsTest extends TestCase
{
    private ManuxiGoogleReviews&MockObject $googleReviews;
    private TwigGoogleReviews $twigExtension;

    protected function setUp(): void
    {
        $this->googleReviews = $this->createMock(ManuxiGoogleReviews::class);
        $this->twigExtension = new TwigGoogleReviews($this->googleReviews);
    }

    public function testExtendsAbstractExtension(): void
    {
        $this->assertInstanceOf(AbstractExtension::class, $this->twigExtension);
    }

    public function testGetFunctionsReturnsTwigFunctions(): void
    {
        $functions = $this->twigExtension->getFunctions();

        $this->assertIsArray($functions);
        $this->assertCount(2, $functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }

    public function testGetFunctionsContainsGetGoogleReviews(): void
    {
        $functions = $this->twigExtension->getFunctions();
        $functionNames = array_map(fn(TwigFunction $f) => $f->getName(), $functions);

        $this->assertContains('get_google_reviews', $functionNames);
    }

    public function testGetFunctionsContainsGetGoogleReviewsCount(): void
    {
        $functions = $this->twigExtension->getFunctions();
        $functionNames = array_map(fn(TwigFunction $f) => $f->getName(), $functions);

        $this->assertContains('get_google_reviews_count', $functionNames);
    }

    public function testGetReviewsWithDefaultParameters(): void
    {
        $expectedReviews = [$this->createReview(), $this->createReview()];

        $this->googleReviews->expects($this->once())
            ->method('getReviews')
            ->with(0, 0)
            ->willReturn($expectedReviews);

        $result = $this->twigExtension->getReviews();

        $this->assertSame($expectedReviews, $result);
    }

    public function testGetReviewsWithOffset(): void
    {
        $expectedReviews = [$this->createReview()];

        $this->googleReviews->expects($this->once())
            ->method('getReviews')
            ->with(2, 0)
            ->willReturn($expectedReviews);

        $result = $this->twigExtension->getReviews(2);

        $this->assertSame($expectedReviews, $result);
    }

    public function testGetReviewsWithOffsetAndLength(): void
    {
        $expectedReviews = [$this->createReview(), $this->createReview(), $this->createReview()];

        $this->googleReviews->expects($this->once())
            ->method('getReviews')
            ->with(1, 3)
            ->willReturn($expectedReviews);

        $result = $this->twigExtension->getReviews(1, 3);

        $this->assertSame($expectedReviews, $result);
    }

    public function testGetReviewsReturnsEmptyArray(): void
    {
        $this->googleReviews->expects($this->once())
            ->method('getReviews')
            ->with(0, 0)
            ->willReturn([]);

        $result = $this->twigExtension->getReviews();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetReviewsCount(): void
    {
        $this->googleReviews->expects($this->once())
            ->method('getReviewsCount')
            ->willReturn(42);

        $result = $this->twigExtension->getReviewsCount();

        $this->assertSame(42, $result);
    }

    public function testGetReviewsCountReturnsZero(): void
    {
        $this->googleReviews->expects($this->once())
            ->method('getReviewsCount')
            ->willReturn(0);

        $result = $this->twigExtension->getReviewsCount();

        $this->assertSame(0, $result);
    }

    public function testGetReviewsReturnType(): void
    {
        $this->googleReviews->method('getReviews')->willReturn([]);

        $result = $this->twigExtension->getReviews();

        $this->assertIsArray($result);
    }

    public function testGetReviewsCountReturnType(): void
    {
        $this->googleReviews->method('getReviewsCount')->willReturn(5);

        $result = $this->twigExtension->getReviewsCount();

        $this->assertIsInt($result);
    }

    private function createReview(): Review
    {
        $review = new Review();
        $review->setAuthorName('Test Author');
        $review->setRating(5);
        $review->setText('Test review');

        return $review;
    }
}
