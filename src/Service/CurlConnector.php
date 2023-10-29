<?php

namespace Manuxi\GoogleReviewsBundle\Service;

use const CURLOPT_HTTPHEADER;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;
use const CURLOPT_USERAGENT;
use Manuxi\GoogleReviewsBundle\Exception\ConnectionException;
use stdClass;

class CurlConnector implements ConnectorInterface
{
    public const STATUS_OK  = 'OK';

    private const ENDPOINT  = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&key=%s&reviews_sort=newest';
    private const USERAGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
    /**
     * @var string
     */
    private $cid;
    /**
     * @var string
     */
    private $apiKey;
    /**
     * @var string
     */
    private $locale;
    /**
     * @var stdClass
     */
    private $result;

    public function __construct($cid, $apiKey, $locale = 'en')
    {
        $this->cid    = $cid;
        $this->apiKey = $apiKey;
        $this->locale = $locale;
    }

    /**
     * @throws ConnectionException
     * @return stdClass
     */
    public function getResult()
    {
        if ($this->hasError()) {
            throw new ConnectionException($this->getDecodedResult()->error_message);
        }
        return $this->getDecodedResult()->result;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        if (self::STATUS_OK === $this->getStatus()) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    private function getStatus()
    {
        return $this->getDecodedResult()->status;
    }

    /**
     * @return string
     */
    private function getResponse()
    {
        if (null === $this->result) {
            $curlHandle = \curl_init();
            \curl_setopt($curlHandle, CURLOPT_URL, $this->getEndpoint());
            \curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            \curl_setopt($curlHandle, CURLOPT_HTTPHEADER, ['Accept-Language: ' . $this->locale]);
            \curl_setopt($curlHandle, CURLOPT_USERAGENT, self::USERAGENT);
            $this->result = \curl_exec($curlHandle);
            \curl_close($curlHandle);
        }

        return $this->result;
    }

    /**
     * @return mixed
     */
    private function getDecodedResult()
    {
        return \json_decode($this->getResponse());
    }

    /**
     * @return string
     */
    private function getEndpoint()
    {
        return \sprintf(
            self::ENDPOINT,
            $this->cid,
            $this->apiKey
        );
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cid . $this->apiKey;
    }
}
