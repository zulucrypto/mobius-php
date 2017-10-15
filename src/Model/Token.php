<?php


namespace ZuluCrypto\MobiusApi\Model;


/**
 * Represents a custom token associated with Mobius
 */
class Token extends RestApiModel
{
    /**
     *
     * @var string
     */
    protected $tokenUid;

    /**
     *
     * @param string $tokenUid Token UID as assigned by Mobius
     */
    public function __construct($tokenUid)
    {
        $this->tokenUid = $tokenUid;
    }

    /**
     * Returns the balance of this token for $address
     *
     * @param $address
     * @return string
     * @throws \ErrorException
     */
    public function getBalance($address)
    {
        $response = $this->apiClient->get('tokens/balance', [
            'token_uid' => $this->tokenUid,
            'address' => $address,
        ]);

        if (!isset($response['balance'])) throw new \ErrorException('Unexpected API response: ' . print_r($response, true));

        return $response['balance'];
    }

    /**
     * @param $isManaged
     * @return array See: https://mobius.network/docs/#create-address
     */
    public function createAddress($isManaged)
    {
        $response = $this->apiClient->post('tokens/create_address', [
            'token_uid' => $this->tokenUid,
            'managed' => $isManaged,
        ]);

        return $response;
    }

    /**
     * @param $address string
     * @return string uid of the new address
     * @throws \ErrorException
     */
    public function registerAddress($address)
    {
        $response = $this->apiClient->post('tokens/register_address', [
            'token_uid' => $this->tokenUid,
            'address' => $address,
        ]);

        if (!isset($response['uid'])) throw new \ErrorException('Unexpected API response: ' . print_r($response, true));

        return $response['uid'];
    }

    /**
     * @param $toAddress
     * @param $numTokens
     * @return string UID of the transfer - used for querying its status (see getTransferInfo)
     * @throws \ErrorException
     */
    public function transferManaged($toAddress, $numTokens)
    {
        $response = $this->apiClient->post('tokens/transfer/managed', [
            'token_uid' => $this->tokenUid,
            'address_to' => $toAddress,
            'num_tokens' => $numTokens,
        ]);

        if (!isset($response['token_address_transfer_uid'])) throw new \ErrorException('Unexpected API response: ' . print_r($response, true));

        return $response['token_address_transfer_uid'];
    }

    /**
     * See: https://mobius.network/docs/#transfer-unmanaged
     *
     * @param      $toAddress
     * @param      $numTokens
     * @param      $privateKey
     * @param null $tokenAddress
     * @param null $gasPrice
     * @param null $gasLimit
     * @return string The transaction hash
     * @throws \ErrorException
     */
    public function transferUnmanaged($toAddress, $numTokens, $privateKey, $tokenAddress = null, $gasPrice = null, $gasLimit = null)
    {
        $parameters = [
            'address_to' => $toAddress,
            'num_tokens' => $numTokens,
            'private_key' => $privateKey,
        ];

        // If $tokenAddress is set, use that instead of the internal tokenUid
        if ($tokenAddress !== null) {
            $parameters['token_address'] = $tokenAddress;
        }
        else {
            $parameters['token_uid'] = $this->tokenUid;
        }

        // Only include gasPrice and gasLimit if they're set
        if ($gasPrice !== null) $parameters['gas_price'] = $gasPrice;
        if ($gasLimit !== null) $parameters['gas_limit'] = $gasLimit;

        $response = $this->apiClient->post('tokens/transfer/unmanaged',
            $parameters
        );

        if (!isset($response['tx_hash'])) throw new \ErrorException('Unexpected API response: ' . print_r($response, true));

        return $response['tx_hash'];
    }

    /**
     * Returns info about a previous transfer
     *
     * @param $transferUid
     * @return array See https://mobius.network/docs/#info
     */
    public function getTransferInfo($transferUid)
    {
        $response = $this->apiClient->get('tokens/transfer/info', [
            'token_address_transfer_uid' => $transferUid,
        ]);

        return $response;
    }

    /**
     * @return string
     */
    public function getTokenUid()
    {
        return $this->tokenUid;
    }

}