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

namespace App\Security\Account;

use App\Security\Credentials\Wargaming as Credentials;
use Symfony\Component\Security\Core\User\UserInterface;

final class Wargaming extends AbstractAccount
{
    public static function fromCredentials(Credentials $cred): UserInterface
    {
        return new self(
            $cred->id,
            $cred->username,
            $cred->region,
            $cred->redirect
        );
    }
}
