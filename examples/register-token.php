<?php

require_once '../vendor/autoload.php';

use \ZuluCrypto\MobiusApi\Mobius;

$API_KEY = getenv('MOBIUS_API_KEY');
if (!$API_KEY) throw new \InvalidArgumentException('Set the MOBIUS_API_KEY environment variable to your mobius API key');

$mobius = new Mobius($API_KEY);

$token = $mobius->registerToken('ERC20', 'test token', 'TEST', '123');

printf('Registered token with UID %s' . PHP_EOL, $token->getTokenUid());