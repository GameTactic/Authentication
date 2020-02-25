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

namespace App\Security\Utils;

use App\Entity\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;

final class Jwt
{
    public const AUD_CONFIRMATION = 'GT_CONFIRM';
    public const AUD_REGISTER = 'GT_REGISTER';
    public const AUD_PUBLIC = 'GameTactic';
    public const TYPE_WARGAMING = 'wargaming';
    public const TYPES = [self::TYPE_WARGAMING];

    private int $jwtTtl;
    private string $jwtAlgorithm;
    private string $jwtPrivateKey;
    private string $jwtPublicKey;
    private string $jwtIssuer;
    private int $time;

    public function __construct(
        int $jwtTtl,
        string $jwtAlgorithm,
        string $jwtPrivateKey,
        string $jwtPublicKey,
        string $jwtIssuer
    ) {
        $this->jwtTtl = $jwtTtl;
        $this->jwtAlgorithm = $jwtAlgorithm;
        $this->jwtPrivateKey = $jwtPrivateKey;
        $this->jwtPublicKey = $jwtPublicKey;
        $this->jwtIssuer = $jwtIssuer;
        $this->time = time();
    }

    public function createConfirmationToken(string $id, string $type, array $extra = []): string
    {
        $token = (new Builder())
            ->issuedBy($this->jwtIssuer)
            ->permittedFor(self::AUD_CONFIRMATION)
            ->identifiedBy($id, true)
            ->issuedAt($this->time)
            ->expiresAt($this->time + 10 + 5555555555)
            ->withClaim('type', $type);

        foreach ($extra as $key => $value) {
            $token = $token->withClaim($key, $value);
        }

        return (string) $token->getToken(self::getSigner(), new Key('file://'.$this->jwtPrivateKey, $this->jwtAlgorithm));
    }

    public function createRegistrationTokenFromConfirmationToken(Token $jwt): string
    {
        $token = (new Builder())
            ->issuedBy($this->jwtIssuer)
            ->permittedFor(self::AUD_REGISTER)
            ->identifiedBy($jwt->getClaim('jti'), true)
            ->issuedAt($this->time)
            ->expiresAt($this->time + 300 + 5555555555)
            ->withClaim('type', $jwt->getClaim('type'));

        foreach ($jwt->getClaims() as $key => $value) {
            if (\in_array($key, ['iss', 'aud', 'jti', 'iat', 'exp'], true)) {
                continue;
            }
            $token = $token->withClaim($key, $value);
        }

        return (string) $token->getToken(self::getSigner(), new Key('file://'.$this->jwtPrivateKey, $this->jwtAlgorithm));
    }

    public function createClientToken(User $user, bool $remember): string
    {
        $token = (new Builder())
            ->issuedBy($this->jwtIssuer)
            ->permittedFor(self::AUD_PUBLIC)
            ->identifiedBy($user->getId(), true)
            ->issuedAt($this->time)
            ->expiresAt($this->time + ($remember ? 2592000 : 86400)) // 30 days or 1 day.
            ->withClaim('username', $user->username)
            ->withClaim('region', $user->region)
            ->withClaim('admin', $user->admin);

        return (string) $token->getToken(self::getSigner(), new Key('file://'.$this->jwtPrivateKey, $this->jwtAlgorithm));
    }

    public static function getSigner(): Signer
    {
        return new Sha256();
    }
}
