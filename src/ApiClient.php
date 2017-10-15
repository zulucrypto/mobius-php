<?php


namespace ZuluCrypto\MobiusApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use ZuluCrypto\MobiusApi\Exception\MobiusApiException;
use ZuluCrypto\MobiusApi\Exception\MobiusApiParameterException;

/**
 * Contains low-level utility methods for making API calls
 */
class ApiClient
{
    /**
     * API key for authenticating with the Mobius API
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Base url (including version) for making API requests
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * @var Client
     */
    private $httpClient;

    public function __construct($apiKey, $baseUrl = 'https://mobius.network/api/v1/')
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;

        $this->httpClient = new Client([
            'base_uri' => $baseUrl,
        ]);
    }

    /**
     * @param       $url
     * @param array $parameters
     * @return array
     */
    public function get($url, $parameters = [])
    {
        $parameters = $this->buildParameters($parameters);

        $requestedUrl = null;
        $response = $this->httpClient->get($url, [
            'exceptions' => false,
            'query' => $parameters,
            'on_stats' => function(TransferStats $stats) use (&$requestedUrl) {
                $requestedUrl = $stats->getEffectiveUri();
            }
        ]);

        return $this->decodeResponse($response, $requestedUrl);
    }

    /**
     * @param       $url
     * @param array $parameters
     * @return array
     */
    public function post($url, $parameters = [])
    {
        $parameters = $this->buildParameters($parameters);

        $requestedUrl = null;
        $response = $this->httpClient->post($url, [
            'exceptions' => false,
            'query' => $parameters,
            'on_stats' => function(TransferStats $stats) use (&$requestedUrl) {
                $requestedUrl = $stats->getEffectiveUri();
            }
        ]);

        return $this->decodeResponse($response, $requestedUrl);
    }

    /**
     * @param ResponseInterface $response
     * @param string $requestedUrl Optional, the requested URL for debugging / error reporting purposes
     * @return mixed
     * @throws \ErrorException
     */
    protected function decodeResponse(ResponseInterface $response, $requestedUrl)
    {
        // Check for status codes that should result in an exception
        switch ($response->getStatusCode()) {
            case 400:
            case 401:
            case 403:
            case 404:
            case 429:
            case 500:
            case 503:
                throw MobiusApiException::fromResponse($response, $requestedUrl);
        }

        $decoded = json_decode($response->getBody()->getContents(), true);

        // Throw an ErrorException if json decoding fails
        if ($decoded === null && json_last_error()) {
            throw new \ErrorException(sprintf('Error decoding API response: %s', json_last_error_msg()));
        }

        // Check for an error in the response and throw an appropriate exception
        if (isset($decoded['error'])) {
            switch ($decoded['error']['type']) {
                case 'invalid_request_error':
                    throw MobiusApiParameterException::fromResponse($response, $requestedUrl);
            }
        }

        // No errors, return the decoded data
        return $decoded;
    }

    /**
     * Adds in any extra parameters necessary to make the API call
     *
     * @param $parameters
     * @return array
     */
    protected function buildParameters($parameters)
    {
        // Add in authentication
        $parameters['api_key'] = $this->apiKey;

        return $parameters;
    }
}