<?php

namespace Manuxi\GoogleReviewsBundle\Model;

class Review
{
    /**
     * @var string
     */
    private $authorName;
    /**
     * @var string
     */
    private $authorUrl;
    /**
     * @var string
     */
    private $language;
    /**
     * @var string
     */
    private $profilePhotoUrl;
    /**
     * @var int
     */
    private $rating;
    /**
     * @var string
     */
    private $relativeTimeDescription;
    /**
     * @var string
     */
    private $text;
    /**
     * @var int
     */
    private $time;

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }

    public function getAuthorUrl(): string
    {
        return $this->authorUrl;
    }

    public function setAuthorUrl(string $authorUrl): void
    {
        $this->authorUrl = $authorUrl;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function getProfilePhotoUrl(): string
    {
        return $this->profilePhotoUrl;
    }

    public function setProfilePhotoUrl(string $profilePhotoUrl): void
    {
        $this->profilePhotoUrl = $profilePhotoUrl;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function getRelativeTimeDescription(): string
    {
        return $this->relativeTimeDescription;
    }

    public function setRelativeTimeDescription(string $relativeTimeDescription): void
    {
        $this->relativeTimeDescription = $relativeTimeDescription;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getTime(): int
    {
        return new $this->time();
    }

    public function setTime(int $time): void
    {
        $this->time = $time;
    }
}
