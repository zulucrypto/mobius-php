<?php


namespace ZuluCrypto\MobiusApi;


use ZuluCrypto\MobiusApi\Exception\MobiusApiException;
use ZuluCrypto\MobiusApi\Model\AppStore;
use ZuluCrypto\MobiusApi\Model\Token;

class Mobius
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @param $apiKey string
     */
    public function __construct($apiKey)
    {
        $this->apiClient = new ApiClient($apiKey);
    }

    /**
     * @param null $appUid If specified, this will be used as the default appUid when calling methods in AppStore
     * @return AppStore
     */
    public function getAppStore($appUid = null)
    {
        $restApi = new AppStore($appUid);
        $restApi->setApiClient($this->apiClient);

        return $restApi;
    }

    /**
     * Registers a new Token
     *
     * @param string $type
     * @param        $name
     * @param        $symbol
     * @param        $address
     * @return Token
     * @throws \ErrorException
     */
    public function registerToken($type = 'ERC20', $name, $symbol, $address)
    {
        $response = $this->apiClient->post('tokens/register', [
            'token_type' => $type,
            'name' => $name,
            'symbol' => $symbol,
            'address' => $address,
        ]);

        if (!isset($response['token_uid'])) throw new \ErrorException('Unexpected API response: ' . print_r($response, true));

        return $this->getToken($response['token_uid']);
    }

    /**
     * @param $tokenUid
     * @return Token
     */
    public function getToken($tokenUid)
    {
        $token = new Token($tokenUid);
        $token->setApiClient($this->apiClient);

        return $token;
    }
}