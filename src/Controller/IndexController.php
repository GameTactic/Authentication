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

namespace App\Controller;

use App\Security\Guard\WargamingAuthenticator;
use App\Security\Utils\Jwt;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class IndexController extends AbstractController
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @Route(name="index", methods={"GET"}, path="/{token}")
     */
    public function __invoke(
        string $jwtIssuer,
        string $jwtPublicKey,
        ?string $token = null
    ): Response {
        return new JsonResponse(
            [
               'issuer'       => $jwtIssuer,
               'audience'     => Jwt::AUD_PUBLIC,
               'publicKey'    => file_get_contents($jwtPublicKey),
               'providers'    => [
                   Jwt::TYPE_WARGAMING => $this->generateWargaming(),
               ],
               'currentToken' => $token,
           ]
        );
    }

    private function generateWargaming(): array
    {
        $return = [];
        foreach (WargamingAuthenticator::REALMS as $REALM) {
            $return[$REALM] = $this->router->generate('connect_wargaming_start', ['region' => $REALM]);
        }

        return $return;
    }
}
