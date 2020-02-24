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

abstract class AbstractCredentials
{
    public string $id;
    public string $username;
    public string $region;
    public string $redirect;

    public function __construct(string $id, string $username, string $region, string $redirect)
    {
        $this->id = $id;
        $this->username = $username;
        $this->region = $region;
        $this->redirect = $redirect;
    }
}
