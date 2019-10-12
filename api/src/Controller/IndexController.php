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

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    /**
     * @param string      $jwtIssuer
     * @param string      $jwtPublicKey
     * @param string|null $token
     *
     * @return Response
     * @Route(name="index", methods={"GET"}, path="/{token}")
     */
    public function __invoke(string $jwtIssuer, string $jwtPublicKey, ?string $token = null): Response
    {
        return new JsonResponse(
           [
               'issuer'       => $jwtIssuer,
               'publicKey'    => file_get_contents($jwtPublicKey),
               'currentToken' => $token,
           ]
       );
    }
}
