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

namespace App\Security\Credentials;

use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Request;

final class Wargaming
{
    /**
     * @var bool
     */
    private $status;
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $id;
    /**
     * @var DateTimeImmutable
     */
    private $expires;
    /**
     * @var string
     */
    private $region;
    /**
     * @var string
     */
    private $redirect;

    /**
     * Wargaming constructor.
     *
     * @param bool              $status
     * @param string            $accessToken
     * @param string            $username
     * @param string            $id
     * @param DateTimeImmutable $expires
     * @param string            $region
     * @param string            $redirect
     */
    public function __construct(
        bool $status,
        string $accessToken,
        string $username,
        string $id,
        DateTimeImmutable $expires,
        string $region,
        string $redirect
    ) {
        $this->status = $status;
        $this->accessToken = $accessToken;
        $this->username = $username;
        $this->id = $id;
        $this->expires = $expires;
        $this->region = $region;
        $this->redirect = $redirect;
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     *
     * @return static
     */
    public static function fromRequest(Request $request): self
    {
        $payload = json_decode(base64_decode($request->get('payload'), true), true, 512, JSON_THROW_ON_ERROR);

        return new self(
            'ok' === $request->get('status', 'error'),
            $request->get('access_token'),
            $request->get('nickname'),
            $request->get('account_id'),
            new DateTimeImmutable(date(DATE_ATOM, $request->get('expires_at', time()))),
            $payload['realm'],
            $payload['redirect']
        );
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getRedirect(): string
    {
        return $this->redirect;
    }
}
