<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\Event;

use Contao\NewsModel;
use Symfony\Component\EventDispatcher\Event;

class AddNewsArticleUrlsEvent extends Event
{
    /**
     * @var string
     */
    protected $baseUrl;
    /** @var NewsModel */
    private $item;
    /** @var array */
    private $urls;

    public function __construct(NewsModel $item, array $urls, string $baseUrl)
    {
        $this->item = $item;
        $this->urls = $urls;
        $this->baseUrl = $baseUrl;
    }

    public function getItem(): NewsModel
    {
        return $this->item;
    }

    public function getUrls(): array
    {
        return $this->urls;
    }

    public function setUrls(array $urls): void
    {
        $this->urls = $urls;
    }

    public function addUrl(string $url): void
    {
        $this->urls[] = $url;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
