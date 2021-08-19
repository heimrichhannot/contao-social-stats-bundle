<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
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

class FacebookStatSource implements StatSourceInterface
{
    const GRAPH_URL = 'https://graph.facebook.com/v11.0/?id=%url%&fields=engagement&access_token=%token%';

    /** @var string|null */
    private $appId;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string|null
     */
    private $appSecret;

    public function __construct(array $bundleConfig)
    {
        $this->appId = $bundleConfig['facebook']['app_id'] ?? null;
        $this->appSecret = $bundleConfig['facebook']['app_secret'] ?? null;
    }

    public static function getName(): string
    {
        return 'Facebook';
    }

    public function prepare(): void
    {
        if (!\is_string($this->appId)) {
            throw new InvalidSetupException('No facebook app id configured.');
        }

        if (!\is_string($this->appSecret)) {
            throw new InvalidSetupException('No facebook app secret configured.');
        }
        $this->client = new Client([]);
    }

    public function updateItem(StatSourceItem $item, array &$data): StatSourceResult
    {
        $result = new StatSourceResult(static::getName());
        $count = 0;

        foreach ($item->getUrls() as $url) {
            $fbUrl = str_replace([
                '%url%',
                '%token%',
            ], [
                urlencode($url),
                $this->appId.'|'.$this->appSecret,
            ],
                static::GRAPH_URL
            );

            try {
                $response = $this->client->request('GET', $fbUrl);
            } catch (ClientException $e) {
                $error = json_decode($e->getResponse()->getBody()->getContents());
                $result->addError($error->error->message);

                continue;
            }

            if ($response && 200 == $response->getStatusCode()) {
                $responseData = json_decode($response->getBody()->getContents(), true);
                $count += (int) $responseData['engagement']['share_count'];
                $result->addVerboseMessage($url.': '.(int) $responseData['engagement']['share_count']);
            }
        }

        $data['facebook'] = $count;
        $result->setCount($count);

        return $result;
    }
}
