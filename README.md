# TSheets by QuickBooks Provider for OAuth 2.0 Client

This package provides [TSheets by QuickBooks](https://tsheetsteam.github.io/api_docs/) OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require benjaminhu/oauth2-tsheets
```
## Usage

Usage is the same as The League's OAuth client, using `\Benjaminhu\OAuth2\Client\Provider\TSheets` as the provider.

### Authorization Code Flow

```php

<?php

session_start();

$provider = new \Benjaminhu\OAuth2\Client\Provider\TSheets([
	'clientId' => '{__TSheets-client-id__}',
	'clientSecret' => '{__TSheets-client-secret__}',
	'redirectUri' => '{__Your-callback-url__}',
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

## License

The MIT License (MIT). Please see [License File](https://github.com/Benjaminhu/oauth2-tsheets/blob/master/LICENSE) for more information.
