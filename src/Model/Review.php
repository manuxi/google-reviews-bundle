<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Model;

class Review
{
    private string $authorName = '';

    private string $authorUrl = '';

    private string $language = '';

    private string $profilePhotoUrl = '';

    private int $rating = 0;

    private string $relativeTimeDescription = '';

    private string $text = '';

    private int $time = 0;

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
        return $this->time;
    }

    public function setTime(int $time): void
    {
        $this->time = $time;
    }
}
