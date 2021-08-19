<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\StatSource;

class StatSourceResult
{
    const COUNT_TYPE_SHARES = 'shares';
    const COUNT_TYPE_VISITS = 'visits';
    const COUNT_TYPE_COMMENTS = 'comments';

    /** @var int */
    private $count;
    /** @var string */
    private $network;
    /** @var string */
    private $message = 'Found %count% shares on <fg=yellow>%network%</>.';
    /** @var array */
    private $errors = [];

    private $countType;

    private $verboseMessages = [];

    /**
     * @param string $message
     */
    public function __construct(string $network, string $countType = self::COUNT_TYPE_SHARES)
    {
        $this->network = $network;
        $this->countType = $countType;
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

    public function getVerboseMessages(): array
    {
        return $this->verboseMessages;
    }

    public function addVerboseMessage(string $message): void
    {
        $this->verboseMessages[] = $message;
    }

    public function getCountType(): string
    {
        return $this->countType;
    }

    public function setCountType(string $countType): void
    {
        $this->countType = $countType;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function setNetwork(string $network): void
    {
        $this->network = $network;
    }
}
