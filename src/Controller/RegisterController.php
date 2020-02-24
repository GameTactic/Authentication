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

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RegisterController extends AbstractJwtAwareController
{
    /**
     * @Route(name="register", methods={"GET"}, path="/register/{token}")
     */
    public function __invoke(string $token): Response
    {
        // Verify token.
        $token = $this->verify($token);
        if (null === $token || !$token->hasClaim('type')) {
            return JsonResponse::create(['error' => 'Not valid', 'status' => 400], 400);
        }
        $user = $this->findUser($token);

        if (null !== $user) {
            return $this->render('register.twig', [
                'token' => '',
                'username' => '',
                'error' => 'registered'
            ]);
        }

        return $this->render('register.twig', [
            'token' => (string) $token,
            'username' => $token->getClaim('username', 'John Doe'),
            'error' => '',
        ]);
    }

    /**
     * @Route(name="register_persist", methods={"POST"}, path="/register/{token}")
     */
    public function persist(Request $request, string $token): Response
    {
        // Verify token.
        $token = $this->verify($token);
        if (null === $token || !$token->hasClaim('type')) {
            return JsonResponse::create(['error' => 'Not valid', 'status' => 400], 400);
        }
        $body = json_decode($request->getContent(), false, JSON_THROW_ON_ERROR);
        $user = new User($body->username, 'eu', new \DateTimeImmutable(), $request->getClientIp());
        $user->{$token->getClaim('type')} = $token->getClaim('jti');
        $this->em->persist($user);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return JsonResponse::create(['error' => 'Username already exists'], 400);
        }

        return JsonResponse::create(
            [
                'token' => $this->jwt->createClientToken($user, false),
                'redirect' => $token->getClaim('redirect'),
            ]);
    }
}
