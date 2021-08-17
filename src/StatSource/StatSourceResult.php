<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\StatSource;

class StatSourceResult
{
    /** @var int */
    private $count = 0;
    /** @var string */
    private $network;
    /** @var string */
    private $message = 'Found %count% shares on %network%.';

    /**
     * @param string $message
     */
    public function __construct(int $count, string $network)
    {
        $this->count = $count;
        $this->network = $network;
    }

    public function getMessage(): string
    {
        return str_replace([
            '%count%',
            '%network%',
        ], [
            $this->count,
            $this->network,
        ],
            $this->message
        );
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
