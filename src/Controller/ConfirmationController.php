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

use App\Security\Utils\Jwt;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConfirmationController extends AbstractJwtAwareController
{
    /**
     * @Route(name="confirm", methods={"GET"}, path="/confirm/{token}")
     */
    public function __invoke(Request $request, string $token): Response
    {
        // Verify token.
        $token = $this->verify($token, Jwt::AUD_CONFIRMATION);
        if (null === $token || !$token->hasClaim('type')) {
            return JsonResponse::create(['error' => 'Not valid', 'status' => 400], 400);
        }
        $user = $this->findUser($token);
        if (null === $user) {
            return new RedirectResponse(
                $this->router->generate(
                    'register',
                    [
                        'token' => $this->jwt->createRegistrationTokenFromConfirmationToken($token),
                    ]
                )
            );
        }
        $user->lastLogin = new \DateTimeImmutable();
        $user->lastIp = $request->getClientIp();
        $this->em->flush();

        return $this->render('confirm.twig', [
            'token'    => $this->jwt->createClientToken($user, false),
            'redirect' => $token->getClaim('redirect'),
            'username' => $user->username,
        ]);
    }
}
