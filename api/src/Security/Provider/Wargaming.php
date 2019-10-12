<?php

/**
 *
 * GameTactic Authentication 2019 — NOTICE OF LICENSE
 *
 * This source file is released under GPLv3 license by copyright holders.
 * Please see LICENSE file for more specific licensing terms.
 * @copyright 2019-2019 (c) GameTactic
 * @author Niko Granö <niko@granö.fi>
 *
 */

namespace App\Security\Provider;

use InvalidArgumentException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use LogicException;
use Psr\Http\Message\ResponseInterface;

final class Wargaming extends AbstractProvider
{
    public const BASE = 'https://api.worldoftanks.%s/wot/auth/login/';

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl(): string
    {
        return '';
    }

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @param string $realm
     *
     * @return string
     */
    public function getBaseAuthorizationUrlOverride(string $realm): string
    {
        // Dirty fix here for NA login.
        if ('na' === $realm) {
            $realm = 'com';
        }

        return sprintf(self::BASE, $realm);
    }

    /**
     * Returns authorization parameters based on provided options.
     *
     * @param array $options
     *
     * @return array Authorization parameters
     */
    protected function getAuthorizationParameters(array $options): array
    {
        $options['redirect_uri'] = $this->redirectUri;
        $options['application_id'] = $this->clientId;

        if (!isset($options['nofollow'])) {
            $options['nofollow'] = 0;
        }

        if (!isset($options['expires_at'])) {
            $options['expires_at'] = time() + 3600;
        }

        if (!isset($options['display'])) {
            $options['display'] = 'page';
        }

        return $options;
    }

    /**
     * Builds the authorization URL.
     *
     * @param array $options
     *
     * @return string Authorization URL
     */
    public function getAuthorizationUrl(array $options = []): string
    {
        if (!isset($options['realm'])) {
            throw new InvalidArgumentException('Option \'realm\' is required to pass.');
        }

        $redirect = ['redirect' => $options['redirect'], 'realm' => $options['realm']];
        $payload = base64_encode(json_encode($redirect, JSON_THROW_ON_ERROR));
        $this->redirectUri .= "/$payload";

        $base = $this->getBaseAuthorizationUrlOverride($options['realm']);
        unset($options['realm']);
        $params = $this->getAuthorizationParameters($options);
        $query = $this->getAuthorizationQuery($params);

        return $this->appendQuery($base, $query);
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return '';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        throw new LogicException('Wargaming doesnt support resource owner URL.');
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * Checks a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param array|string      $data     Parsed response data
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        // Do nothing for now.
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param array       $response
     * @param AccessToken $token
     *
     * @return ResourceOwnerInterface|void
     */
    protected function createResourceOwner(array $response, AccessToken $token): void
    {
        throw new LogicException('Resource owner is not supported for Wargaming.');
    }
}
