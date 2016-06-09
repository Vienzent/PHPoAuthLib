<?php namespace OAuth\OAuth2\Service;

use OAuth\Common\Exception\Exception;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Token\TokenInterface;

class Shopify extends AbstractService
{
	protected $shopDomain;

	/**
     * Defined scopes - More scopes are listed here:
     * https://help.shopify.com/api/guides/authentication/oauth#scopes
     */

	// READ
	const SCOPE_READ_CONTENT 			= 'read_content';
	const SCOPE_READ_THEMES 			= 'read_themes';
	const SCOPE_READ_PRODUCTS 			= 'read_products';
	const SCOPE_READ_CUSTOMER = 'read_customers';
	const SCOPE_READ_ORDERS = 'read_orders';
	const SCOPE_READ_SCRIPT_TAGS = 'read_script_tags';
	const SCOPE_READ_FULFILLMENTS = 'read_fulfillments';
	const SCOPE_READ_SHIPPING = 'read_shipping';
	const SCOPE_READ_USERS = 'read_users';
	const SCOPE_READ_ANALYTICS = 'read_analytics';

	// WRITE
	const SCOPE_WRITE_CONTENT = 'WRITE_content';
	const SCOPE_WRITE_THEMES = 'WRITE_themes';
	const SCOPE_WRITE_PRODUCTS = 'WRITE_products';
	const SCOPE_WRITE_CUSTOMER = 'WRITE_customers';
	const SCOPE_WRITE_ORDERS = 'WRITE_orders';
	const SCOPE_WRITE_SCRIPT_TAGS = 'WRITE_script_tags';
	const SCOPE_WRITE_FULFILLMENTS = 'WRITE_fulfillments';
	const SCOPE_WRITE_SHIPPING = 'WRITE_shipping';
	const SCOPE_WRITE_USERS = 'WRITE_users';

	public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes ,$baseApiUri);

        $shopDomain = $credentials->getShopDomain();
        $this->shopDomain = $shopDomain;
        $this->baseApiUri = new Uri('https://' . $shopDomain . '/admin/');
        // if (null === $baseApiUri) {
        //     $this->baseApiUri = new Uri('https://api.github.com/');
        // }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://'.$this->shopDomain .'/admin/oauth/authorize');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://'.$this->shopDomain .'/admin/oauth/access_token');
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);

        if (isset($data['expires'])) {
            $token->setLifeTime($data['expires']);
        }

        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token']);
        unset($data['expires']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    protected function getScopesDelimiter()
    {
        return ',';
    }

    public function request($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        $uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);
        $token = $this->storage->retrieveAccessToken($this->service());

        if ($token->getEndOfLife() !== TokenInterface::EOL_NEVER_EXPIRES
            && $token->getEndOfLife() !== TokenInterface::EOL_UNKNOWN
            && time() > $token->getEndOfLife()
        ) {
            throw new ExpiredTokenException(
                sprintf(
                    'Token expired on %s at %s',
                    date('m/d/Y', $token->getEndOfLife()),
                    date('h:i:s A', $token->getEndOfLife())
                )
            );
        }
        $extraHeaders['X-Shopify-Access-Token'] = $token->getAccessToken();
        $extraHeaders = array_merge($this->getExtraApiHeaders(), $extraHeaders);

        return $this->httpClient->retrieveResponse($uri, $body, $extraHeaders, $method);
    }

}