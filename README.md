# Mobius PHP API Client
Provides simple access to the [Mobius API](https://mobius.network/docs/) for applications written in PHP

## Installation

Install with composer:

```bash
$ composer require zulucrypto/mobius-php
```

## Quickstart

This library provides an object-oriented way to interact with the Mobius API:

```php
// Main object for working with the API
$mobius = new Mobius($API_KEY);

// Retrieves an object to work with the DApp store
$appStore = $mobius->getAppStore($APP_UID);

// Easily query a user's balance
$userBalance = $appStore->getBalance($EMAIL);

// Use 5 MOBI and get the user's updated balance
$userBalance = $appStore->useBalance($EMAIL, 5);

```

## Objects

 * `Mobius` - All main API objects are available from this class
 * `AppStore` - Methods for working with a user's balance
 * `Token` - Methods for working with ERC20 tokens

## More information

 * Examples are available in the `examples` directory
 * For detailed response information, see [the Mobius API REST documentation](https://mobius.network/docs/)

   
