<?php


namespace ZuluCrypto\MobiusApi\Model;


use ZuluCrypto\MobiusApi\ApiClient;

abstract class RestApiModel
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @return ApiClient
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * @param ApiClient $apiClient
     */
    public function setApiClient($apiClient)
    {
        $this->apiClient = $apiClient;
    }
}