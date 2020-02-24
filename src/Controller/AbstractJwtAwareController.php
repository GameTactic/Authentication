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
use App\Security\Utils\Jwt;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractJwtAwareController extends AbstractController
{
    protected EntityManagerInterface $em;
    protected string $jwtIssuer;
    protected string $jwtPublicKey;
    protected RouterInterface $router;
    protected Jwt $jwt;

    public function __construct(
        EntityManagerInterface $em,
        string $jwtIssuer,
        string $jwtPublicKey,
        RouterInterface $router,
        Jwt $jwt
    ) {
        $this->em = $em;
        $this->jwtIssuer = $jwtIssuer;
        $this->jwtPublicKey = $jwtPublicKey;
        $this->router = $router;
        $this->jwt = $jwt;
    }

    protected function verify(string $token): ?Token
    {
        $data = new ValidationData();
        $data->setIssuer($this->jwtIssuer);
        $data->setAudience(Jwt::AUD_CONFIRMATION);
        $data->setCurrentTime(time());
        $token = (new Parser())->parse($token);
        $valid = $token->verify(Jwt::getSigner(), file_get_contents($this->jwtPublicKey)) && $token->validate($data);

        return $valid ? $token : null;
    }

    protected function findUser(Token $token): ?User
    {
        $type = $token->getClaim('type');
        if (!\in_array($type, Jwt::TYPES, true)) {
            throw new \LogicException('Type not supported!');
        }
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy([$type => $token->getClaim('jti')]);

        return $user;
    }
}
