<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Service;

use stdClass;

interface ConnectorInterface
{
    public function getResult(): stdClass;

    public function hasError(): bool;

    public function getCacheKey(): string;
}
