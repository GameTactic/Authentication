<?php

declare(strict_types=1);

/**
 *
 * GameTactic Authentication 2020 — NOTICE OF LICENSE
 *
 * This source file is released under GPLv3 license by copyright holders.
 * Please see LICENSE file for more specific licensing terms.
 * @copyright 2019-2020 (c) GameTactic
 * @author Niko Granö <niko@granö.fi>
 *
 */

namespace App\Security\Guard;

use App\Security\Account\Wargaming as Account;
use App\Security\Credentials\Wargaming as Credentials;
use App\Security\Utils\Jwt;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class WargamingAuthenticator extends AbstractRemoteAuthenticator
{
    public const REALMS = ['ru', 'eu', 'na', 'asia'];
    protected const CHECK_ROUTE = 'connect_wargaming_check';
    protected const CREDENTIALS = Credentials::class;
    protected const ACCOUNT = Account::class;

    /**
     * @param $credentials Credentials
     */
    protected function validateCredentials($credentials): bool
    {
        return $credentials->status;
    }

    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @param string $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response
    {
        /** @var Account $user */
        $user = $token->getUser();

        return $this->handleResponse(
            $user->getId(),
            Jwt::TYPE_WARGAMING,
            [
                'region'   => $user->getRegion(),
                'redirect' => $user->getRedirect(),
                'username' => $user->getUsername(),
            ]
        );
    }
}
