<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Service;

use Manuxi\GoogleReviewsBundle\Exception\ConnectionException;
use stdClass;

class CurlConnector implements ConnectorInterface
{
    public const STATUS_OK = 'OK';

    private const ENDPOINT = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&key=%s&reviews_sort=newest';

    private const USERAGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';

    private ?string $response = null;

    private ?stdClass $decodedResult = null;

    public function __construct(
        private readonly string $cid,
        private readonly string $apiKey,
        private readonly string $locale = 'en',
    ) {
    }

    /**
     * @throws ConnectionException
     */
    public function getResult(): stdClass
    {
        if ($this->hasError()) {
            $decoded = $this->getDecodedResult();
            $message = $decoded->error_message ?? 'Unknown error from Google API';

            throw new ConnectionException($message);
        }

        return $this->getDecodedResult()->result;
    }

    public function hasError(): bool
    {
        return self::STATUS_OK !== $this->getStatus();
    }

    private function getStatus(): string
    {
        $decoded = $this->getDecodedResult();

        return $decoded->status ?? 'ERROR';
    }

    /**
     * @throws ConnectionException
     */
    private function getResponse(): string
    {
        if (null === $this->response) {
            $curlHandle = \curl_init();
            \curl_setopt($curlHandle, \CURLOPT_URL, $this->getEndpoint());
            \curl_setopt($curlHandle, \CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($curlHandle, \CURLOPT_HTTPHEADER, ['Accept-Language: ' . $this->locale]);
            \curl_setopt($curlHandle, \CURLOPT_USERAGENT, self::USERAGENT);
            \curl_setopt($curlHandle, \CURLOPT_TIMEOUT, 30);

            $result = \curl_exec($curlHandle);
            $error = \curl_error($curlHandle);
            \curl_close($curlHandle);

            if (false === $result) {
                throw new ConnectionException('cURL error: ' . $error);
            }

            $this->response = $result;
        }

        return $this->response;
    }

    /**
     * @throws ConnectionException
     */
    private function getDecodedResult(): stdClass
    {
        if (null === $this->decodedResult) {
            $response = $this->getResponse();
            $decoded = \json_decode($response);

            if (!\is_object($decoded)) {
                throw new ConnectionException('Invalid JSON response from Google API');
            }

            $this->decodedResult = $decoded;
        }

        return $this->decodedResult;
    }

    private function getEndpoint(): string
    {
        return \sprintf(
            self::ENDPOINT,
            $this->cid,
            $this->apiKey
        );
    }

    public function getCacheKey(): string
    {
        return $this->cid . $this->apiKey;
    }
}
