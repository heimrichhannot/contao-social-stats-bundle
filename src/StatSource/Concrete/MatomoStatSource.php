<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SocialStatsBundle\StatSource\Concrete;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use HeimrichHannot\SocialStatsBundle\Exception\InvalidSetupException;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceInterface;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceItem;
use HeimrichHannot\SocialStatsBundle\StatSource\StatSourceResult;

class MatomoStatSource implements StatSourceInterface
{
    const API = '%matomo%?module=API&method=Actions.getPageUrl&pageUrl=%url%/&idSite=1&period=range&date=%start_date%,today&format=json&token_auth=%token%';

    /**
     * @var Client
     */
    private $client;
    /**
     * @var string|null
     */
    private $matomoUrl;
    /**
     * @var string|null
     */
    private $token;

    /**
     * @param Client $client
     */
    public function __construct(array $bundleConfig)
    {
        $this->matomoUrl = $bundleConfig['matomo']['url'] ?? null;
        $this->token = $bundleConfig['matomo']['token'] ?? null;
    }

    public static function getName(): string
    {
        return 'Matomo';
    }

    public function prepare(): void
    {
        if (!$this->matomoUrl) {
            throw new InvalidSetupException('Missing matomo url.');
        }

        if (!$this->token) {
            throw new InvalidSetupException('Missing matomo access token.');
        }
        $this->client = new Client([]);
    }

    public function updateItem(StatSourceItem $item, array &$data): StatSourceResult
    {
        $api = str_replace(['%matomo%', '%token%', '%start_date%'], [$this->matomoUrl, $this->token, date('Y-m-d', $item->getStartDate())], static::API);

        $result = new StatSourceResult($this::getName());
        $count = 0;

        foreach ($item->getUrls() as $url) {
            $urlCount = 0;
            $path = trim(parse_url($url, \PHP_URL_PATH), '/');

            if (false === $path) {
                $result->addError("URL $url is malformed");

                continue;
            }

            if (empty($path)) {
                $result->addError("URL $url is not a path to an news article");

                continue;
            }
            $query = str_replace(['%url%'], [$path], $api);

            try {
                $response = $this->client->request('GET', $query);
            } catch (ClientException $e) {
                if (!empty($e->getResponse()->getBody()->getContents())) {
                    $error = json_decode($e->getResponse()->getBody()->getContents())->error->message;
                } else {
                    $error = $e->getMessage();
                }
                $result->addError($error);

                continue;
            }

            if ($response && 200 == $response->getStatusCode()) {
                $content = json_decode($response->getBody()->getContents(), true);

                if (!empty($content)) {
                    $rowCount = 0;

                    foreach ($content as $entry) {
                        $rowCount += $entry['nb_hits'] ?? $entry['nb_visits'] ?? 0;
                    }
                    $urlCount += $rowCount;
                }
            }
            $result->addVerboseMessage($url.': '.$urlCount);
            $count += $urlCount;
        }

        $data['matomo'] = $count;
        $result->setCount($count);

        return $result;
    }
}
