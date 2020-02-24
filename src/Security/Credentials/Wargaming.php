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

namespace App\Security\Credentials;

use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Request;

final class Wargaming extends AbstractCredentials
{
    public bool $status;
    public string $accessToken;
    public DateTimeImmutable $expires;

    public function __construct(
        string $id,
        string $username,
        string $region,
        string $redirect,
        string $accessToken,
        DateTimeImmutable $expires,
        bool $status
    ) {
        $this->accessToken = $accessToken;
        $this->expires = $expires;
        $this->status = $status;
        parent::__construct($id, $username, $region, $redirect);
    }

    /**
     * @throws Exception
     *
     * @return static
     */
    public static function fromRequest(Request $request): self
    {
        $payload = json_decode(base64_decode($request->get('payload'), true), true, 64, JSON_THROW_ON_ERROR);

        return new self(
            $request->get('account_id'),
            $request->get('nickname'),
            $payload['realm'],
            $payload['redirect'],
            $request->get('access_token'),
            new DateTimeImmutable(date(DATE_ATOM, (int) $request->get('expires_at', time()))),
            'ok' === $request->get('status', 'error'),
        );
    }
}
