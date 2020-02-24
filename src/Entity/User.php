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

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string")
     */
    private string $id;
    /**
     * @ORM\Column(type="string", unique=true)
     */
    public string $username;
    /**
     * @ORM\Column(type="string")
     */
    public string $region;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $lastLogin;
    /**
     * @ORM\Column(type="string")
     */
    public string $lastIp;
    /**
     * @ORM\Column(type="boolean")
     */
    public bool $admin = false;
    /**
     * @ORM\Column(type="boolean")
     */
    public bool $banned = false;
    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    public ?string $wargaming = null;

    public function __construct(
        string $username,
        string $region,
        DateTimeImmutable $lastLogin,
        string $lastIp
    ) {
        $this->username = $username;
        $this->region = $region;
        $this->lastLogin = $lastLogin;
        $this->lastIp = $lastIp;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
