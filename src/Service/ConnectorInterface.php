<?php

namespace Manuxi\GoogleReviewsBundle\Service;

use stdClass;

interface ConnectorInterface
{
    /**
     * @return stdClass
     */
    public function getResult();

    /**
     * @return bool
     */
    public function hasError();

    /**
     * @return string
     */
    public function getCacheKey();
}
