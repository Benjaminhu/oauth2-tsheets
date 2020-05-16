<?php

namespace Benjaminhu\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class TSheets extends AbstractProvider
{
	/**
	 * @var string Key used in a token response to identify the resource owner.
	 */
	const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'user_id';

	/**
	 * Base URL
	 *
	 * @var string
	 */
	public $baseUrl = 'https://rest.tsheets.com/api/v1';

	/**
	 * Get authorization url to begin OAuth flow
	 *
	 * @return string
	 */
	public function getBaseAuthorizationUrl()
	{
		return $this->baseUrl . '/authorize';
	}

	/**
	 * Get access token url to retrieve token
	 *
	 * @param  array $params
	 *
	 * @return string
	 */
	public function getBaseAccessTokenUrl(array $params)
	{
		return $this->baseUrl . '/grant';
	}


	/**
	 * Get provider url to fetch user details
	 *
	 * @param  AccessToken $token
	 *
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl(AccessToken $token)
	{
		return $this->getAuthenticatedUrlForEndpoint('/current_user', $token);
	}

	/**
	 * Get the full uri with appended oauth_token query string
	 *
	 * @param string $endpoint | with leading slash
	 * @param AccessToken $token
	 * @return string
	 */
	public function getAuthenticatedUrlForEndpoint($endpoint, AccessToken $token)
	{
		return $this->baseUrl . $endpoint . '?oauth_token=' . $token->getToken();
	}

	/**
	 * @return array
	 */
	protected function getDefaultScopes()
	{
		return array();
	}

	protected function checkResponse(ResponseInterface $response, $data)
	{
		$statusCode = $response->getStatusCode();
		if ($statusCode >= 400) {
			throw new IdentityProviderException(
				isset($data[0]['message']) ? $data[0]['message'] : $response->getReasonPhrase(),
				$statusCode,
				$response
			);
		}
	}

	protected function createResourceOwner(array $response, AccessToken $token)
	{
		$owner_id = $token->getResourceOwnerId();
		if (isset($response['results']['users'][$owner_id])) {
			$response = $response['results']['users'][$owner_id];
			// TODO: $response['supplemental_data']['jobcodes']
		}
		return new TSheetsResourceOwner($response);
	}

	/**
	 * Adds token to headers
	 *
	 * @param AccessToken $token
	 * @return array
	 */
	protected function getAuthorizationHeaders($token = null) {
		if (isset($token)) {
			return array (
				'Authorization' => 'Bearer ' . $token->getToken()
			);
		}
		return array();
	}
}
