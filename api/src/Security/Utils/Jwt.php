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

namespace App\Security\Utils;

use App\Security\Account\Wargaming;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

final class Jwt
{
    /**
     * @var int
     */
    private $jwtTtl;
    /**
     * @var string
     */
    private $jwtAlgorithm;
    /**
     * @var string
     */
    private $jwtPrivateKey;
    /**
     * @var string
     */
    private $jwtPublicKey;
    /**
     * @var string
     */
    private $jwtIssuer;

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
    }

    public function createWargaming(Wargaming $wargaming): string
    {
        $time = time();
        $token = (new Builder())
            ->issuedBy($this->jwtIssuer)
            ->permittedFor($this->jwtIssuer)
            ->identifiedBy($wargaming->getId(), true)
            ->issuedAt($time)
            ->expiresAt($time + $this->jwtTtl)
            ->withClaim('username', $wargaming->getUsername())
            ->withClaim('region', $wargaming->getRegion())
            ->withClaim('uid', $wargaming->getId())
            ->getToken(new Sha256(), new Key('file://'.$this->jwtPrivateKey, $this->jwtAlgorithm));

        return (string) $token;
    }
}