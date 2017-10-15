<?php


namespace ZuluCrypto\MobiusApi\Model;


/**
 * Methods for working with the DApp store
 */
class AppStore extends RestApiModel
{
    /**
     * The default APP UID to use when making requests
     *
     * @var string
     */
    protected $defaultAppUid;

    public function __construct($appUid = null)
    {
        $this->defaultAppUid = $appUid;
    }

    /**
     * Returns the user's balance
     *
     * @param        $email
     * @param string $appUid
     * @return int
     * @throws \ErrorException
     */
    public function getBalance($email, $appUid = null)
    {
        if (null === $appUid) $appUid = $this->defaultAppUid;
        if (!$appUid) throw new \InvalidArgumentException('No appUid specified');

        $response = $this->apiClient->get('app_store/balance', [
            'app_uid' => $appUid,
            'email' => $email,
        ]);

        if (!isset($response['num_credits'])) throw new \ErrorException('Unexpected API response');
        return $response['num_credits'];
    }

    /**
     * Uses $numCredits for $email
     *
     * @param      $email
     * @param      $numCredits
     * @param null $appUid
     * @return int the user's current balance (after using $numCredits)
     * @throws \ErrorException
     */
    public function useBalance($email, $numCredits, $appUid = null)
    {
        if (null === $appUid) $appUid = $this->defaultAppUid;
        if (!$appUid) throw new \InvalidArgumentException('No appUid specified');

        $response = $this->apiClient->post('app_store/use', [
            'app_uid' => $appUid,
            'email' => $email,
            'num_credits' => $numCredits,
        ]);

        if (!isset($response['success']) || !$response['success']) throw new \ErrorException('API call did not succeed');
        if (!isset($response['num_credits'])) throw new \ErrorException('Unexpected API response');

        return $response['num_credits'];
    }
}