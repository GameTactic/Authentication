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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractJwtAwareController
{
    /**
     * @Route(name="profile", methods={"GET"}, path="/profile/{token}/{redirect}")
     */
    public function __invoke(string $token, string $redirect = ''): Response
    {
        // Verify token.
        $token = $this->verify($token, Jwt::AUD_PUBLIC);
        if (null === $token) {
            return JsonResponse::create(['error' => 'Not valid', 'status' => 400], 400);
        }
        $user = $this->findUser($token, true);

        if (null === $user) {
            return new JsonResponse(['error' => 'User not found!']);
        }
        $user = get_object_vars($user);
        $user['lastLogin'] = $user['lastLogin']->format('Y/m/d h:i:s');

        return $this->render('profile.twig', [
            'token'    => (string) $token,
            'user'     => $user,
            'redirect' => $redirect,
        ]);
    }
}
