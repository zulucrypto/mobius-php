<?php

require_once '../vendor/autoload.php';

use \ZuluCrypto\MobiusApi\Mobius;
use \ZuluCrypto\MobiusApi\Exception\MobiusApiException;

$API_KEY = getenv('MOBIUS_API_KEY');
if (!$API_KEY) throw new \InvalidArgumentException('Set the MOBIUS_API_KEY environment variable to your mobius API key');

$APP_UID = 'YOUR_APP_ID_HERE';
$EMAIL   = 'EMAIL_HERE';

$mobius = new Mobius($API_KEY);

$appStore = $mobius->getAppStore($APP_UID);

try {
    $userBalance = $appStore->getBalance($EMAIL);

    printf('Balance for %s is %s' . PHP_EOL, $EMAIL, $userBalance);

    // Use some MOBI if the user has at least 1 in credit
    if ($userBalance > 1) {
        printf('Spending 1 credit...' . PHP_EOL);
        $newBalance = $appStore->useBalance($EMAIL, 1);

        printf('Balance is now %s' . PHP_EOL, $newBalance);
    }
    else {
        printf('No credits to spend!' . PHP_EOL);
    }
}
// MobiusApiException provides detailed error information
catch (MobiusApiException $e)
{
    printf("URL    : %s" . PHP_EOL, $e->getUrl());
    printf("Error  : %s" . PHP_EOL, $e->getMessage());
    printf("Details: %s" . PHP_EOL, $e->getMobiusDetails());
    printf("Trace  : \n%s" . PHP_EOL, $e->getTraceAsString());
}
