# TSheets by QuickBooks Provider for OAuth 2.0 Client

This package provides [TSheets by QuickBooks](https://tsheetsteam.github.io/api_docs/) OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require liftkeeper/oauth2-tsheets
```
## Usage

Usage is the same as The League's OAuth client, using `\Liftkeeper\OAuth2\Client\Provider\TSheets` as the provider.

TSheets by QuickBooks: [Obtaining an API Access Token](https://tsheetsteam.github.io/api_docs/?php#obtaining-an-api-access-token)

### Authorization Code Flow

```php

<?php

require '{__PATH_TO_YOUR_VENDOR_DIRECTORY__}/autoload.php';

session_start();

$provider = new \Liftkeeper\OAuth2\Client\Provider\TSheets([
	'clientId' => '{__TSHEETS-CLIENT-ID__}',
	'clientSecret' => '{__TSHEETS-CLIENT-SECRET__}',
	'redirectUri' => '{__YOUR-CALLBACK-URL__}',
]);

if (!isset($_GET['code'])) {
	$authUrl = $provider->getAuthorizationUrl();
	$_SESSION['oauth2state'] = $provider->getState();
	header('Location: ' . $authUrl);
	exit;

} else if (empty($_GET['state']) || ( $_GET['state'] !== $_SESSION['oauth2state'] )) {
	unset($_SESSION['oauth2state']);
	exit('Invalid state');

} else {
	try {
		$accessToken = $provider->getAccessToken('authorization_code', [
			'code' => $_GET['code'],
		]);

		echo '<pre>';
		echo 'Token: ' . $accessToken->getToken() . PHP_EOL;
		echo 'RefreshToken: ' . $accessToken->getRefreshToken() . PHP_EOL;
		echo 'Expires: ' . $accessToken->getExpires() . PHP_EOL;
		echo 'hasExpired: ' . ( $accessToken->hasExpired() ? 'expired' : 'not expired' ) . PHP_EOL;

		$resourceOwner = $provider->getResourceOwner($accessToken);

		print_r($resourceOwner);
		echo '</pre>';

	} catch (Exception $e) {
		exit($e->getMessage());

	} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
		exit($e->getMessage());
	}
}

```

### Refresh Token Code Flow

```php

<?php

$provider = new \Liftkeeper\OAuth2\Client\Provider\TSheets([
	'clientId' => '{__TSHEETS-CLIENT-ID__}',
	'clientSecret' => '{__TSHEETS-CLIENT-SECRET__}',
	'redirectUri' => '{__YOUR-CALLBACK-URL__}',
]);

// load stored accessToken
// $accessToken = loadAccessToken();

$newAccessToken = $provider->getAccessToken('refresh_token', [
	'grant_type' => 'refresh_token',
	'access_token' => $accessToken
]);

// save new accessToken
// saveAccessToken($newAccessToken);


```

## License

The MIT License (MIT). Please see [License File](https://github.com/Liftkeeper/oauth2-tsheets/blob/master/LICENSE) for more information.
