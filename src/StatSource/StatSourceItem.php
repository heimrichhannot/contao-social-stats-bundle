<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\StatSource;

use Contao\NewsModel;

class StatSourceItem
{
    /** @var string */
    private $baseUrl;
    /** @var NewsModel */
    private $model;
    /** @var array */
    private $urls;

    public function __construct(NewsModel $model, array $urls, string $baseUrl)
    {
        $this->model = $model;
        $this->urls = $urls;
        $this->baseUrl = $baseUrl;
    }

    public function getModel(): NewsModel
    {
        return $this->model;
    }

    public function getUrls(): array
    {
        return $this->urls;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
