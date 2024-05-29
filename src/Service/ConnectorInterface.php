<?php

namespace Manuxi\GoogleReviewsBundle\Service;

use stdClass;

interface ConnectorInterface
{
    /**
     * @return stdClass
     */
    public function getResult(): stdClass;

    /**
     * @return bool
     */
    public function hasError(): bool;

    /**
     * @return string
     */
    public function getCacheKey(): string;
}
