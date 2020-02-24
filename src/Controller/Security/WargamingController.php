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

namespace App\Controller\Security;

use App\Security\Guard\WargamingAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class WargamingController extends AbstractController
{
    /**
     * NOTE: Keep this function before start function in this file!
     *
     * @Route(name="connect_wargaming_check", methods={"GET"}, path="/connect/wargaming/callback/{payload}")
     *
     * @see WargamingAuthenticator
     */
    public function chech(?string $payload = null): void
    {
        throw new \LogicException('No guards defined for Wargaming!');
    }

    /**
     * @param string $redirect
     *
     * @Route(
     *     name="connect_wargaming_start",
     *     methods={"GET"},
     *     path="/connect/wargaming/{region}/{redirect}",
     *     requirements={"redirect"=".+"}
     *     )
     */
    public function start(ClientRegistry $registry, Request $request, string $region = '', $redirect = ''): Response
    {
        $region = mb_strtolower($region);
        if (!\in_array($region, WargamingAuthenticator::REALMS, true)) {
            return new Response(
                'Region not supported. Supported regions are '
                .implode(', ', WargamingAuthenticator::REALMS).'. Given \''.$region.'\'.',
                400
            );
        }

        return $registry->getClient('wargaming')->redirect([], ['realm' => $region, 'redirect' => $redirect]);
    }
}
