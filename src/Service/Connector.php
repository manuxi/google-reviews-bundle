<?php

namespace manuxi\GoogleBusinessDataBundle\Service;

use const CURLOPT_HTTPHEADER;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;
use const CURLOPT_USERAGENT;
use manuxi\GoogleBusinessDataBundle\Exception\ConnectionException;
use stdClass;

class Connector
{
    public const STATUS_OK  = 'OK';

    private const ENDPOINT  = 'https://maps.googleapis.com/maps/api/place/details/json?cid=%s&key=%s';
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
     */
    public function getResult(): stdClass
    {
        if ($this->hasError()) {
            throw new ConnectionException($this->getDecodedResult()->error_message);
        }
        return $this->getDecodedResult()->result;
    }

    public function hasError(): bool
    {
        if (self::STATUS_OK === $this->getStatus()) {
            return false;
        }
        return true;
    }

    public function getStatus(): string
    {
        return $this->getDecodedResult()->status;
    }

    private function getResponse(): string
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

    private function getDecodedResult()
    {
        return \json_decode($this->getResponse());
    }

    private function getEndpoint(): string
    {
        return \sprintf(
            self::ENDPOINT,
            $this->cid,
            $this->apiKey
        );
    }

    public function getCacheKey()
    {
        return $this->cid . $this->apiKey;
    }
}
