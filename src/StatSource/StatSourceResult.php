<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\StatSource;

class StatSourceResult
{
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';

    /** @var int */
    private $count;
    /** @var string */
    private $network;
    /** @var string */
    private $message = 'Found %count% shares on %network%.';
    /** @var array */
    private $errors = [];

    /**
     * @param string $message
     */
    public function __construct(string $network)
    {
        $this->network = $network;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
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
