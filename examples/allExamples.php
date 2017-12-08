<?php

/*

 YOU CAN ALSO USE PHP CLI TO RUN THIS EXAMPLE SCRIPT 

*/
 
require_once '../vendor/autoload.php';

use \ZuluCrypto\MobiusApi\Mobius;
use \ZuluCrypto\MobiusApi\Exception\MobiusApiException;

$API_KEY = 'API_KEY_HERE'; 
$APP_UID = 'APP_UID_HERE';
$EMAIL   = 'YOUR_EMAIL_HERE';

$mobius = new Mobius($API_KEY);
$appStore = $mobius->getAppStore($APP_UID);

try{


		/*
		    END POINT: GET https://mobius.network/api/v1/app_store/balance
		*/
		$userBalance = $appStore->getBalance($EMAIL);
		printf(PHP_EOL.'- Balance for %s is : %s' .PHP_EOL,$EMAIL,$userBalance);


		/*
		    END POINT: POST https://mobius.network/api/v1/app_store/use
		*/
		$newBalance = $appStore->useBalance($EMAIL, 1);
		echo '- After spending it becomes: '.$newBalance.PHP_EOL;




		/*
		    END POINT: POST https://mobius.network/api/v1/tokens/register
		*/

		$token = $mobius->registerToken('erc20', 'test token', 'TEST', '0xE94327D07Fc17907b4DB788E5aDf2ed424adDff6');
		printf('- Registered token with UID : %s'.PHP_EOL, $token->getTokenUid());



		/*
		    END POINT: POST https://mobius.network/api/v1/tokens/create_address
		*/

		$address = $token->createAddress(true); // managed (bool) true/false
		printf('- Address created from token created by `registerToken` method : %s'.PHP_EOL,$address['address']);


		/*
		    END POINT: POST https://mobius.network/api/v1/tokens/create_address
		*/

		$response = $token->registerAddress($address['address']); // address obtained from `createAddress` method
		printf('- `UID` of the new address created from address created `createAddress` method  :  %s'.PHP_EOL,$response);

		$newAdd=$response;


		/*
		    GET https://mobius.network/api/v1/tokens/balance
		*/
		$response = $token->getBalance($address['address']);
		printf('- `Balance` of the new address created or address registered  :  %s'.PHP_EOL,$response);



		/*
		    POST https://mobius.network/api/v1/tokens/transfer/managed

		    COMMENTS:
		    THIS ENDPOINT THROWS error about Missing required param: token_address_uid
		*/

		$response = $token->transferManaged($address['address'], 0);
		printf('- `Transfer` tokens from a Mobius managed address :  %s'.PHP_EOL,$response);


		/*
		    POST https://mobius.network/api/v1/tokens/transfer/managed
		*/

		$response = $token->getTransferInfo($token_address_uid);
		printf('- Get the `transaction` hash of a Mobius managed token transfer. :  %s'.PHP_EOL,$response);



}catch (MobiusApiException $e){
    // print_r($e);
    printf("Error  : %s" . PHP_EOL, $e->getMobiusDetails());
}

echo PHP_EOL;